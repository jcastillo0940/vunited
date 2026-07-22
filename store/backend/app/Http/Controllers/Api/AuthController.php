<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
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

        $admin = Admin::query()->where('email', $credentials['email'])->first();

        if (! $admin || ! $admin->is_active || ! Hash::check($credentials['password'], $admin->password)) {
            throw ValidationException::withMessages(['email' => ['Credenciales invalidas.']]);
        }

        $admin->update(['last_login_at' => now()]);
        $token = $admin->createToken($credentials['device_name']);

        return response()->json([
            'token' => $token->plainTextToken,
            'admin' => [
                'id' => $admin->id,
                'name' => $admin->name,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesion cerrada.']);
    }
}
