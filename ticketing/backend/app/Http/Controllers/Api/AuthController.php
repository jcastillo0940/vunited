<?php

namespace App\Http\Controllers\Api;

use App\Models\Operator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $operator = Operator::query()->where('email', $credentials['email'])->first();

        if (! $operator || ! $operator->is_active || ! Hash::check($credentials['password'], $operator->password)) {
            throw ValidationException::withMessages(['email' => ['Credenciales invalidas.']]);
        }

        $operator->update(['last_login_at' => now()]);
        $token = $operator->createToken($credentials['device_name']);

        return response()->json([
            'token' => $token->plainTextToken,
            'operator' => [
                'id' => $operator->id,
                'name' => $operator->name,
                'role' => $operator->role,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesion cerrada.']);
    }
}
