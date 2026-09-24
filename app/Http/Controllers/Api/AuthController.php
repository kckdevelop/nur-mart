<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
                'telepon' => $user->telepon,
                'alamat' => $user->alamat,
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
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'created_at' => $user->created_at,
        ], 'Data profil berhasil diambil.');
    }

    /**
     * Update profile pengguna yang sedang login
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
            'password_lama' => 'nullable|string',
            'password_baru' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan pengguna lain.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi profil gagal', 422, $validator->errors());
        }

        $validated = $validator->validated();

        // Jika ingin mengubah password, cek password lama
        if (!empty($validated['password_baru'])) {
            if (empty($validated['password_lama'])) {
                return $this->errorResponse('Password lama wajib diisi untuk mengubah password.', 422, [
                    'password_lama' => ['Password lama wajib diisi.']
                ]);
            }

            if (!Hash::check($validated['password_lama'], $user->password)) {
                return $this->errorResponse('Password lama tidak sesuai.', 422, [
                    'password_lama' => ['Password lama yang Anda masukkan salah.']
                ]);
            }

            $user->password = Hash::make($validated['password_baru']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->telepon = $validated['telepon'] ?? null;
        $user->alamat = $validated['alamat'] ?? null;
        $user->save();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'updated_at' => $user->updated_at,
        ], 'Profil pengguna berhasil diperbarui.');
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
