<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Ticketing\Models\OperatorAssignment;
use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OperatorController
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Operator::query()->get(['id', 'name', 'email', 'role', 'is_active', 'last_login_at']),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:operators,email'],
            'role' => ['required', 'in:admin,gate_operator,viewer'],
        ]);

        $password = Str::password(20);
        $operator = Operator::create([
            ...$data,
            'password' => Hash::make($password),
            'is_active' => true,
        ]);

        // La contrasena generada solo se devuelve en esta respuesta (nunca
        // se guarda en texto plano ni se vuelve a poder consultar).
        return response()->json([
            'id' => $operator->id,
            'email' => $operator->email,
            'temporary_password' => $password,
        ], 201);
    }

    public function assign(Request $request, int $operatorId): JsonResponse
    {
        $data = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'door_id' => ['nullable', 'exists:doors,id'],
        ]);

        $assignment = OperatorAssignment::firstOrCreate([
            'operator_id' => $operatorId,
            'event_id' => $data['event_id'],
            'door_id' => $data['door_id'] ?? null,
        ]);

        return response()->json($assignment, 201);
    }

    public function revoke(int $operatorId): JsonResponse
    {
        Operator::whereKey($operatorId)->update(['is_active' => false]);
        // Revoca tambien todas las sesiones API activas (Sanctum) de inmediato.
        Operator::find($operatorId)?->tokens()->delete();

        return response()->json(['message' => 'Operador desactivado.']);
    }
}
