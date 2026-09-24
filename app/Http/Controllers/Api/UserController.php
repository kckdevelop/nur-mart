<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * Tampilkan daftar seluruh pengguna/kasir
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->select(['id', 'name', 'email', 'role', 'telepon', 'alamat', 'created_at']);

        // Filter berdasarkan role jika ada
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Pencarian nama atau email atau telepon
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('telepon', 'like', "%{$keyword}%");
            });
        }

        $users = $query->orderBy('role', 'asc')->orderBy('name', 'asc')->get();

        return $this->successResponse($users, 'Daftar pengguna berhasil diambil.');
    }

    /**
     * Tambah user / kasir baru
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:pemilik,kasir',
            'telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus pemilik atau kasir.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $validated = $validator->validated();
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'created_at' => $user->created_at,
        ], 'Pengguna berhasil ditambahkan.', 201);
    }

    /**
     * Tampilkan detail pengguna
     */
    public function show(int $id): JsonResponse
    {
        $user = User::select(['id', 'name', 'email', 'role', 'telepon', 'alamat', 'created_at'])->find($id);

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan.', 404);
        }

        return $this->successResponse($user, 'Detail pengguna berhasil diambil.');
    }

    /**
     * Perbarui data pengguna
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan.', 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:pemilik,kasir',
            'telepon' => 'nullable|string|max:30',
            'alamat' => 'nullable|string|max:500',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar pada pengguna lain.',
            'password.min' => 'Password minimal 6 karakter jika ingin diubah.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus pemilik atau kasir.',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validasi gagal', 422, $validator->errors());
        }

        $validated = $validator->validated();

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'updated_at' => $user->updated_at,
        ], 'Data pengguna berhasil diperbarui.');
    }

    /**
     * Hapus pengguna
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $currentUser = $request->user();

        if ($currentUser && $currentUser->id === $id) {
            return $this->errorResponse('Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.', 400);
        }

        $user = User::find($id);

        if (!$user) {
            return $this->errorResponse('Pengguna tidak ditemukan.', 404);
        }

        // Cek jika pengguna memiliki riwayat penjualan atau belanja
        if ($user->penjualans()->exists() || $user->belanjas()->exists()) {
            return $this->errorResponse('Pengguna tidak dapat dihapus karena memiliki riwayat transaksi kasir atau belanja.', 400);
        }

        $user->delete();

        return $this->successResponse(null, 'Pengguna berhasil dihapus.');
    }
}
