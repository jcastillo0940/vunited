<?php

namespace App\Http\Controllers\Api;

use App\Domain\Ticketing\Models\Ticket;
use App\Domain\Wallets\Exceptions\WalletNotConfiguredException;
use App\Domain\Wallets\Services\AppleWalletService;
use App\Domain\Wallets\Services\GoogleWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController
{
    public function __construct(
        private readonly GoogleWalletService $google,
        private readonly AppleWalletService $apple,
    ) {}

    public function google(Request $request, string $publicId): JsonResponse
    {
        $ticket = Ticket::query()->where('public_id', $publicId)->with('order')->first();
        if (! $ticket) {
            return response()->json(['message' => 'Boleto no encontrado.'], 404);
        }

        if ($ticket->order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Este boleto no te pertenece.'], 403);
        }

        try {
            return response()->json(['save_url' => $this->google->buildSaveLink($ticket)]);
        } catch (WalletNotConfiguredException $e) {
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function apple(Request $request, string $publicId): JsonResponse
    {
        $ticket = Ticket::query()->where('public_id', $publicId)->with('order')->first();
        if (! $ticket) {
            return response()->json(['message' => 'Boleto no encontrado.'], 404);
        }

        if ($ticket->order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Este boleto no te pertenece.'], 403);
        }

        try {
            $this->apple->buildSignedPkpass($ticket);
        } catch (WalletNotConfiguredException $e) {
            return response()->json(['message' => $e->getMessage()], 501);
        }

        // Inalcanzable mientras no haya certificado; el metodo de arriba
        // siempre lanza WalletNotConfiguredException hasta entonces.
        return response()->json(['message' => 'No implementado.'], 501);
    }
}
