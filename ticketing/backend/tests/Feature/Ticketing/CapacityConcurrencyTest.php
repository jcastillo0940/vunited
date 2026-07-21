<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Exceptions\InsufficientCapacityException;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\Seat;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Services\CapacityService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Prueba de concurrencia real (no simulada): usa pcntl_fork para que
 * procesos hijos independientes, cada uno con su propia conexion a MySQL,
 * intenten reclamar el mismo cupo al mismo tiempo. Si CapacityService no
 * fuera atomico, mas de un hijo podria tener exito con cupo=1 (sobreventa).
 */
class CapacityConcurrencyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // No usamos RefreshDatabase (transacciones) a proposito: los
        // procesos hijos necesitan ver datos ya confirmados (commit) del
        // padre a traves de su propia conexion.
        DB::table('validation_events')->delete();
        DB::table('tickets')->delete();
        DB::table('order_items')->delete();
        DB::table('holds')->delete();
        DB::table('orders')->delete();
        DB::table('seats')->delete();
        DB::table('zones')->delete();
        DB::table('events')->delete();
    }

    public function test_two_simultaneous_claims_for_the_last_unit_only_one_succeeds(): void
    {
        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('pcntl no disponible en este entorno.');
        }

        $event = Event::create([
            'code' => 'concurrency-test-1',
            'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(10),
        ]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general',
            'kind' => 'general', 'price' => 5, 'capacity_total' => 1,
            'capacity_available' => 1, 'capacity_held' => 0,
        ]);

        $results = $this->runInForkedChildren(concurrency: 10, task: function () use ($zone) {
            // Nueva conexion por hijo: forzar reconexion (fork no comparte sockets de forma segura).
            DB::purge();
            $service = new CapacityService;
            try {
                $service->claimGeneralCapacity($zone->fresh(), 1);

                return 'success';
            } catch (InsufficientCapacityException) {
                return 'rejected';
            }
        });

        $successes = count(array_filter($results, fn ($r) => $r === 'success'));
        $rejections = count(array_filter($results, fn ($r) => $r === 'rejected'));

        $this->assertSame(1, $successes, 'Exactamente 1 de 10 intentos concurrentes debe ganar el ultimo cupo.');
        $this->assertSame(9, $rejections, 'Los otros 9 deben ser rechazados, no sobrevender.');

        $zone->refresh();
        $this->assertSame(0, $zone->capacity_available);
        $this->assertSame(1, $zone->capacity_held);
        $this->assertGreaterThanOrEqual(0, $zone->capacity_available, 'La capacidad nunca debe ser negativa.');
    }

    public function test_two_simultaneous_claims_for_the_same_seat_only_one_succeeds(): void
    {
        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('pcntl no disponible en este entorno.');
        }

        $event = Event::create([
            'code' => 'concurrency-test-2',
            'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(10),
        ]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'VIP', 'slug' => 'vip',
            'kind' => 'seated', 'price' => 25, 'capacity_total' => 1,
            'capacity_available' => 1, 'capacity_held' => 0,
        ]);
        $seat = Seat::create(['zone_id' => $zone->id, 'label' => 'A-1', 'status' => 'available']);

        $results = $this->runInForkedChildren(concurrency: 10, task: function () use ($seat) {
            DB::purge();
            $service = new CapacityService;
            try {
                $service->claimSeat($seat->fresh());

                return 'success';
            } catch (InsufficientCapacityException) {
                return 'rejected';
            }
        });

        $successes = count(array_filter($results, fn ($r) => $r === 'success'));
        $this->assertSame(1, $successes, 'Exactamente 1 de 10 intentos concurrentes debe ganar el asiento unico.');

        $seat->refresh();
        $this->assertSame('held', $seat->status);
    }

    /**
     * @return array<int, string>
     */
    private function runInForkedChildren(int $concurrency, \Closure $task): array
    {
        $pipes = [];
        $pids = [];

        for ($i = 0; $i < $concurrency; $i++) {
            $pipe = [];
            socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $pipe);
            $pid = pcntl_fork();

            if ($pid === -1) {
                $this->fail('No se pudo hacer fork para la prueba de concurrencia.');
            }

            if ($pid === 0) {
                // Hijo.
                socket_close($pipe[0]);
                $result = ($task)();
                $payload = (string) $result;
                socket_write($pipe[1], $payload, strlen($payload));
                socket_close($pipe[1]);
                exit(0);
            }

            socket_close($pipe[1]);
            $pipes[$i] = $pipe[0];
            $pids[] = $pid;
        }

        $results = [];
        foreach ($pipes as $i => $sock) {
            $data = '';
            while ($chunk = socket_read($sock, 64)) {
                $data .= $chunk;
            }
            socket_close($sock);
            $results[$i] = $data;
        }

        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        return $results;
    }
}
