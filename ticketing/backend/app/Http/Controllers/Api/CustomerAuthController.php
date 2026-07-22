<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerAuthController
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('customers', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $customer = Customer::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'last_login_at' => now(),
        ]);

        $token = $customer->createToken($data['device_name']);

        return response()->json([
            'token' => $token->plainTextToken,
            'customer' => ['id' => $customer->public_id, 'name' => $customer->name, 'email' => $customer->email],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $customer = Customer::query()->where('email', $credentials['email'])->first();

        if (! $customer || ! Hash::check($credentials['password'], $customer->password)) {
            throw ValidationException::withMessages(['email' => ['Credenciales invalidas.']]);
        }

        $customer->update(['last_login_at' => now()]);
        $token = $customer->createToken($credentials['device_name']);

        return response()->json([
            'token' => $token->plainTextToken,
            'customer' => ['id' => $customer->public_id, 'name' => $customer->name, 'email' => $customer->email],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesion cerrada.']);
    }

    public function me(Request $request): JsonResponse
    {
        $customer = $request->user();

        return response()->json([
            'customer' => ['id' => $customer->public_id, 'name' => $customer->name, 'email' => $customer->email],
        ]);
    }
}
