<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Services\CapacityService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Prueba de concurrencia mas alta (50 compradores simultaneos para 20
 * cupos). No se probo a 100/300/1000 en ESTE servidor porque es la misma
 * VM que sirve produccion (united.wp-pa.com) con MySQL max_connections=151
 * y solo 2 CPU / ~3.8GB RAM totales - abrir cientos de conexiones
 * concurrentes de prueba arriesgaria la disponibilidad de produccion real.
 * Ver docs/operations/phase7-load-testing.md para el limite documentado y
 * la recomendacion de correr niveles mas altos en una instancia separada.
 */
class HighConcurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::table('validation_events')->delete();
        DB::table('tickets')->delete();
        DB::table('order_items')->delete();
        DB::table('holds')->delete();
        DB::table('orders')->delete();
        DB::table('seats')->delete();
        DB::table('zones')->delete();
        DB::table('events')->delete();
    }

    public function test_50_concurrent_buyers_for_20_units_never_oversells(): void
    {
        if (! extension_loaded('pcntl') || ! extension_loaded('sockets')) {
            $this->markTestSkipped('pcntl/sockets no disponibles.');
        }

        $capacity = 20;
        $concurrency = 50;

        $event = Event::create(['code' => 'load-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(5)]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general', 'kind' => 'general',
            'price' => 5, 'capacity_total' => $capacity, 'capacity_available' => $capacity, 'capacity_held' => 0,
        ]);

        $pipes = [];
        $pids = [];
        for ($i = 0; $i < $concurrency; $i++) {
            $pair = [];
            socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $pair);
            $pid = pcntl_fork();
            if ($pid === 0) {
                socket_close($pair[0]);
                DB::purge();
                $result = 'rejected';
                try {
                    (new CapacityService)->claimGeneralCapacity($zone->fresh(), 1);
                    $result = 'success';
                } catch (InsufficientCapacityException) {
                    // esperado para los que pierden la carrera
                }
                socket_write($pair[1], $result, strlen($result));
                socket_close($pair[1]);
                exit(0);
            }
            socket_close($pair[1]);
            $pipes[$i] = $pair[0];
            $pids[] = $pid;
        }

        $results = [];
        foreach ($pipes as $i => $sock) {
            $data = '';
            while ($chunk = socket_read($sock, 32)) {
                $data .= $chunk;
            }
            socket_close($sock);
            $results[$i] = $data;
        }
        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        $successes = count(array_filter($results, fn ($r) => $r === 'success'));
        $rejections = count(array_filter($results, fn ($r) => $r === 'rejected'));

        $this->assertSame($capacity, $successes, "Exactamente {$capacity} de {$concurrency} compradores concurrentes deben ganar cupo.");
        $this->assertSame($concurrency - $capacity, $rejections);

        $zone->refresh();
        $this->assertSame(0, $zone->capacity_available);
        $this->assertSame($capacity, $zone->capacity_held);
        $this->assertGreaterThanOrEqual(0, $zone->capacity_available);
    }

    public function test_duplicate_event_code_is_rejected(): void
    {
        Event::create(['code' => 'dup-code', 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(1)]);

        $this->expectException(QueryException::class);
        Event::create(['code' => 'dup-code', 'home_team' => 'C', 'away_team' => 'D', 'starts_at' => now()->addDays(2)]);
    }
}
