<?php

namespace App\Console\Commands;

use App\Domain\Squad\Models\Player;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportTransfermarktSquad extends Command
{
    protected $signature = 'squad:import-transfermarkt
                            {--club=48217        : Transfermarkt club ID}
                            {--category=first-team : Categoría destino (first-team, academy, women-team)}
                            {--dry-run           : Muestra qué importaría sin guardar}
                            {--fresh             : Desactiva placeholders de esa categoría antes de importar}';

    protected $description = 'Importa/actualiza plantilla desde Transfermarkt API';

    private const API_BASE = 'https://transfermarkt-api.fly.dev';

    private const VALID_CATEGORIES = ['first-team', 'academy', 'women-team'];

    private const POSITION_KEY_MAP = [
        'Goalkeeper'          => 'goalkeeper',
        'Centre-Back'         => 'defender',
        'Left-Back'           => 'defender',
        'Right-Back'          => 'defender',
        'Defender'            => 'defender',
        'Defensive Midfield'  => 'midfielder',
        'Central Midfield'    => 'midfielder',
        'Left Midfield'       => 'midfielder',
        'Right Midfield'      => 'midfielder',
        'Attacking Midfield'  => 'midfielder',
        'Midfielder'          => 'midfielder',
        'Left Winger'         => 'forward',
        'Right Winger'        => 'forward',
        'Second Striker'      => 'forward',
        'Centre-Forward'      => 'forward',
        'Striker'             => 'forward',
    ];

    private const POSITION_ES_MAP = [
        'Goalkeeper'          => 'Portero',
        'Centre-Back'         => 'Def. Central',
        'Left-Back'           => 'Lateral Izquierdo',
        'Right-Back'          => 'Lateral Derecho',
        'Defender'            => 'Defensa',
        'Defensive Midfield'  => 'Mediocentro Def.',
        'Central Midfield'    => 'Mediocentro',
        'Left Midfield'       => 'Volante Izquierdo',
        'Right Midfield'      => 'Volante Derecho',
        'Attacking Midfield'  => 'Mediapunta',
        'Midfielder'          => 'Volante',
        'Left Winger'         => 'Extremo Izquierdo',
        'Right Winger'        => 'Extremo Derecho',
        'Second Striker'      => 'Segundo Delantero',
        'Centre-Forward'      => 'Delantero Centro',
        'Striker'             => 'Delantero',
    ];

    public function handle(): int
    {
        $clubId   = $this->option('club');
        $category = $this->option('category');
        $dryRun   = $this->option('dry-run');
        $fresh    = $this->option('fresh');

        if (! in_array($category, self::VALID_CATEGORIES)) {
            $this->error("Categoría inválida: {$category}. Usa: " . implode(', ', self::VALID_CATEGORIES));
            return 1;
        }

        $this->info("Obteniendo plantilla del club ID {$clubId} → categoría [{$category}]...");

        $squadRes = Http::timeout(30)->get(self::API_BASE . "/clubs/{$clubId}/players");

        if ($squadRes->failed()) {
            $this->error('No se pudo obtener la plantilla: HTTP ' . $squadRes->status());
            return 1;
        }

        $squad = $squadRes->json('players', []);
        $this->info("Se encontraron " . count($squad) . " jugadores.");

        if ($fresh && ! $dryRun) {
            // Desactiva solo los placeholders (sin tm_id) de esa categoría
            $deactivated = Player::query()
                ->where('category', $category)
                ->where(function ($q) {
                    $q->whereNull('stats')
                      ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(stats, '$.tm_id')) IS NULL");
                })
                ->update(['is_active' => false]);

            $this->info("--fresh: {$deactivated} placeholder(s) de [{$category}] desactivados.");
        }

        $sortOrder = $this->getNextSortOrder($category);

        foreach ($squad as $entry) {
            $tmId = (string) $entry['id'];

            $this->line("  → [{$tmId}] {$entry['name']} ({$entry['position']})");

            $profileRes   = Http::timeout(20)->get(self::API_BASE . "/players/{$tmId}/profile");
            $profile      = $profileRes->ok() ? $profileRes->json() : [];

            $achieveRes   = Http::timeout(20)->get(self::API_BASE . "/players/{$tmId}/achievements");
            $achievements = $achieveRes->ok() ? $this->formatAchievements($achieveRes->json('achievements', [])) : [];

            $data = $this->buildPlayerData($entry, $profile, $achievements, $category, $sortOrder++);

            if ($dryRun) {
                $this->table(
                    ['Campo', 'Valor'],
                    collect($data)->except(['stats'])->map(fn ($v, $k) => [$k, is_array($v) ? json_encode($v, JSON_UNESCAPED_UNICODE) : $v])->values()->toArray()
                );
                continue;
            }

            $player = Player::where(function ($q) use ($tmId, $data) {
                $q->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(stats, '$.tm_id')) = ?", [$tmId])
                  ->orWhere('slug', $data['slug']);
            })->first();

            if ($player) {
                $player->update($data);
                $this->line("     <info>Actualizado:</info> {$player->name}");
            } else {
                Player::create($data);
                $this->line("     <comment>Creado:</comment> {$data['name']}");
            }

            usleep(300_000);
        }

        if (! $dryRun) {
            $this->newLine();
            $this->info('Importación completa. Jugadores procesados: ' . count($squad));
        }

        return 0;
    }

    private function getNextSortOrder(string $category): int
    {
        return (int) Player::where('category', $category)->max('sort_order') + 1;
    }

    private function buildPlayerData(array $entry, array $profile, array $achievements, string $category, int $sortOrder): array
    {
        $tmPosition = $entry['position'] ?? '';
        $height     = $profile['height'] ?? $entry['height'] ?? null;
        $foot       = $profile['foot'] ?? $entry['foot'] ?? null;

        $number = null;
        if (! empty($profile['shirtNumber'])) {
            $number = ltrim($profile['shirtNumber'], '#');
        }

        $photoPath   = $profile['imageUrl'] ?? null;
        $citizenship = $profile['citizenship'] ?? $entry['nationality'] ?? [];
        $nationality = is_array($citizenship) ? ($citizenship[0] ?? null) : $citizenship;

        $biography = null;
        if (! empty($profile['nameInHomeCountry'])) {
            $biography = 'Nombre completo: ' . $profile['nameInHomeCountry'];
        }
        if (! empty($profile['placeOfBirth']['city'])) {
            $biography .= "\nLugar de nacimiento: " . $profile['placeOfBirth']['city'] . ', ' . $profile['placeOfBirth']['country'];
        }
        if (! empty($profile['marketValue'])) {
            $biography .= "\nValor de mercado: €" . number_format($profile['marketValue'] / 1000, 0) . 'K';
        }

        $stats = [
            'tm_id'        => (string) $entry['id'],
            'market_value' => $profile['marketValue'] ?? $entry['marketValue'] ?? null,
            'joined_on'    => $entry['joinedOn'] ?? null,
            'contract'     => $entry['contract'] ?? null,
            'signed_from'  => $entry['signedFrom'] ?? null,
        ];

        return [
            'name'          => $profile['name'] ?? $entry['name'],
            'slug'          => Str::slug($profile['name'] ?? $entry['name']),
            'number'        => $number,
            'position'      => self::POSITION_ES_MAP[$tmPosition] ?? $tmPosition,
            'position_key'  => self::POSITION_KEY_MAP[$tmPosition] ?? null,
            'category'      => $category,
            'birth_date'    => $entry['dateOfBirth'] ?? null,
            'nationality'   => $nationality,
            'height'        => $height ? $height . ' cm' : null,
            'dominant_foot' => $foot ? ucfirst($foot) : null,
            'photo_path'    => $photoPath,
            'biography'     => $biography,
            'stats'         => $stats,
            'achievements'  => $achievements ?: null,
            'is_active'     => true,
            'sort_order'    => $sortOrder,
        ];
    }

    private function formatAchievements(array $raw): array
    {
        $result = [];

        foreach ($raw as $item) {
            $title = $item['title'] ?? '';

            foreach ($item['details'] ?? [] as $detail) {
                $competition = $detail['competition']['name'] ?? ($detail['club']['name'] ?? '');
                $season      = $detail['season']['name'] ?? '';
                $parts       = array_filter([$competition, $season]);

                $result[] = $parts ? $title . ' — ' . implode(' ', $parts) : $title;
            }
        }

        return array_values(array_unique($result));
    }
}
