<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('nim', $request->nim)->first();

        if (
            ! $user
            || ! $user->is_active
            || ! in_array($user->role, ['mahasiswa', 'dosen'], true)
            || ! Hash::check($request->password, $user->password)
        ) {
            return response()->json(['message' => 'NIM atau password salah'], 401);
        }

        $token = Str::random(64);
        $user->update(['api_token' => hash('sha256', $token)]);

        return response()->json([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'nim' => $user->nim,
                'role' => $user->role,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'name' => $user->name,
                'nim' => $user->nim,
                'role' => $user->role,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->update(['api_token' => null]);

        return response()->json(['message' => 'Logout berhasil']);
    }
}
