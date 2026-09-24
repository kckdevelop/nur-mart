<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Login user & create Sanctum Token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('Email atau password salah.', 401);
        }

        $deviceName = $request->device_name ?? 'mobile_app';
        $token = $user->createToken($deviceName, ["role:{$user->role}"])->plainTextToken;

        return $this->successResponse([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Login berhasil.');
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ], 'Data profil berhasil diambil.');
    }

    /**
     * Refresh Sanctum token (revoke current token and issue new one)
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $user = $request->user();
        $deviceName = $request->header('User-Agent') ?? 'mobile_app';

        // Revoke the current token that was used for the request
        $request->user()->currentAccessToken()->delete();

        // Generate a new token
        $newToken = $user->createToken($deviceName, ["role:{$user->role}"])->plainTextToken;

        return $this->successResponse([
            'token' => $newToken,
            'token_type' => 'Bearer',
        ], 'Token berhasil diperbarui (refreshed).');
    }

    /**
     * Logout user & revoke token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logout berhasil, sesi telah dihapus.');
    }
}
