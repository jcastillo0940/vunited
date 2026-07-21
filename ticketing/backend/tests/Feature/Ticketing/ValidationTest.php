<?php

namespace Tests\Feature\Ticketing;

use App\Domain\Ticketing\Models\Door;
use App\Domain\Ticketing\Models\Event;
use App\Domain\Ticketing\Models\OperatorAssignment;
use App\Domain\Ticketing\Models\Order;
use App\Domain\Ticketing\Models\OrderItem;
use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Ticketing\Models\Zone;
use App\Domain\Ticketing\Services\TicketValidationService;
use App\Domain\Ticketing\Support\TicketQrSigner;
use App\Models\Operator;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    // Sin RefreshDatabase a proposito: test_simultaneous_double_scan_only_one_wins
    // usa pcntl_fork, y los hijos (conexiones propias a MySQL) no pueden ver
    // datos creados dentro de una transaccion sin confirmar del padre.
    // Limpiamos manualmente en su lugar.
    protected function setUp(): void
    {
        parent::setUp();
        DB::table('validation_events')->delete();
        DB::table('tickets')->delete();
        DB::table('order_items')->delete();
        DB::table('operator_assignments')->delete();
        DB::table('doors')->delete();
        DB::table('orders')->delete();
        DB::table('zones')->delete();
        DB::table('events')->delete();
        DB::table('operators')->delete();
    }

    private function issuedTicket(): Ticket
    {
        $event = Event::create(['code' => 'val-'.uniqid(), 'home_team' => 'A', 'away_team' => 'B', 'starts_at' => now()->addDays(2)]);
        $zone = Zone::create([
            'event_id' => $event->id, 'name' => 'General', 'slug' => 'general', 'kind' => 'general',
            'price' => 5, 'capacity_total' => 10, 'capacity_available' => 9, 'capacity_held' => 0,
        ]);
        $order = Order::create(['event_id' => $event->id, 'status' => 'paid', 'customer_email' => 'a@example.com']);
        $item = OrderItem::create(['order_id' => $order->id, 'zone_id' => $zone->id, 'quantity' => 1, 'unit_price' => 5, 'line_total' => 5]);
        $ticket = Ticket::create([
            'order_id' => $order->id, 'order_item_id' => $item->id, 'event_id' => $event->id,
            'zone_id' => $zone->id, 'status' => 'issued', 'qr_token' => 'placeholder', 'issued_at' => now(),
        ]);
        $ticket->update(['qr_token' => app(TicketQrSigner::class)->sign($ticket)]);

        return $ticket->fresh();
    }

    public function test_first_scan_is_valid(): void
    {
        $ticket = $this->issuedTicket();
        $result = app(TicketValidationService::class)->validate($ticket->qr_token, null, null, null, 'corr-1');

        $this->assertTrue($result['valid']);
        $this->assertSame('valid', $result['result']);
        $this->assertSame('used', $ticket->fresh()->status);
    }

    public function test_second_scan_is_rejected_as_already_used(): void
    {
        $ticket = $this->issuedTicket();
        $service = app(TicketValidationService::class);

        $service->validate($ticket->qr_token, null, null, null, 'corr-1');
        $second = $service->validate($ticket->qr_token, null, null, null, 'corr-2');

        $this->assertFalse($second['valid']);
        $this->assertSame('already_used', $second['result']);
    }

    public function test_simultaneous_double_scan_only_one_wins(): void
    {
        if (! extension_loaded('pcntl')) {
            $this->markTestSkipped('pcntl no disponible.');
        }

        $ticket = $this->issuedTicket();
        $token = $ticket->qr_token;

        $pipes = [];
        $pids = [];
        for ($i = 0; $i < 5; $i++) {
            $pair = [];
            socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $pair);
            $pid = pcntl_fork();
            if ($pid === 0) {
                socket_close($pair[0]);
                DB::purge();
                $result = app(TicketValidationService::class)->validate($token, null, null, null, 'corr-'.$i);
                $payload = $result['result'];
                socket_write($pair[1], $payload, strlen($payload));
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
            while ($chunk = socket_read($sock, 64)) {
                $data .= $chunk;
            }
            socket_close($sock);
            $results[$i] = $data;
        }
        foreach ($pids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        $valid = count(array_filter($results, fn ($r) => $r === 'valid'));
        $rejected = count(array_filter($results, fn ($r) => $r === 'already_used'));

        $this->assertSame(1, $valid, 'Exactamente 1 de 5 escaneos simultaneos debe marcar el ticket como usado.');
        $this->assertSame(4, $rejected, 'Los otros 4 deben ser rechazados como already_used, nunca validos.');
    }

    public function test_altered_qr_is_rejected_as_invalid(): void
    {
        $ticket = $this->issuedTicket();
        $tampered = substr_replace($ticket->qr_token, 'zzzz', 3, 4);

        $result = app(TicketValidationService::class)->validate($tampered, null, null, null, 'corr-x');

        $this->assertFalse($result['valid']);
        $this->assertSame('invalid', $result['result']);
        $this->assertSame('issued', $ticket->fresh()->status, 'Un QR alterado nunca debe marcar el ticket real como usado.');
    }

    public function test_revoked_ticket_is_rejected(): void
    {
        $ticket = $this->issuedTicket();
        $ticket->update(['status' => 'revoked', 'revoked_at' => now(), 'revoked_reason' => 'test']);

        $result = app(TicketValidationService::class)->validate($ticket->qr_token, null, null, null, 'corr-r');

        $this->assertFalse($result['valid']);
        $this->assertSame('revoked', $result['result']);
    }

    public function test_wrong_event_door_is_rejected(): void
    {
        $ticket = $this->issuedTicket();
        $otherEvent = Event::create(['code' => 'other-'.uniqid(), 'home_team' => 'X', 'away_team' => 'Y', 'starts_at' => now()->addDays(1)]);
        $door = Door::create(['event_id' => $otherEvent->id, 'name' => 'Puerta 1']);

        $result = app(TicketValidationService::class)->validate($ticket->qr_token, $door->id, null, null, 'corr-w');

        $this->assertFalse($result['valid']);
        $this->assertSame('wrong_event', $result['result']);
        $this->assertSame('issued', $ticket->fresh()->status);
    }

    public function test_operator_without_assignment_is_rejected(): void
    {
        $ticket = $this->issuedTicket();
        $door = Door::create(['event_id' => $ticket->event_id, 'name' => 'Puerta 1']);
        $operator = Operator::create(['name' => 'Op', 'email' => 'op-'.uniqid().'@example.com', 'password' => 'x', 'role' => 'gate_operator', 'is_active' => true]);
        // Sin OperatorAssignment para este evento/puerta.

        $result = app(TicketValidationService::class)->validate($ticket->qr_token, $door->id, $operator->id, null, 'corr-p');

        $this->assertFalse($result['valid']);
        $this->assertSame('wrong_door', $result['result']);
    }

    public function test_assigned_operator_can_validate(): void
    {
        $ticket = $this->issuedTicket();
        $door = Door::create(['event_id' => $ticket->event_id, 'name' => 'Puerta 1']);
        $operator = Operator::create(['name' => 'Op', 'email' => 'op-'.uniqid().'@example.com', 'password' => 'x', 'role' => 'gate_operator', 'is_active' => true]);
        OperatorAssignment::create(['operator_id' => $operator->id, 'event_id' => $ticket->event_id, 'door_id' => $door->id]);

        $result = app(TicketValidationService::class)->validate($ticket->qr_token, $door->id, $operator->id, null, 'corr-ok');

        $this->assertTrue($result['valid']);
    }
}
