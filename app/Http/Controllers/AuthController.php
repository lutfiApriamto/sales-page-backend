<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Fitur Register
    public function register(Request $request)
    {
        // 1. Validasi input dari frontend
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' butuh input 'password_confirmation' dari frontend
        ]);

        // 2. Buat user baru ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password otomatis di-hash
        ]);

        // 3. Buat token API untuk sesi login
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Jika gagal, kirim JSON error secara eksplisit
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Password Salah'
            ], 401);
        }

        // Bersihkan token lama
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // Response minimalis sesuai kebutuhan lu
        return response()->json([
            'status' => 'success',
            'token'  => $token,
            'user'   => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    // Fitur Logout
    public function logout(Request $request)
    {
        // Menghapus token yang sedang digunakan (Revoke token)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil log out'
        ]);
    }

    // Tambahkan method ini di dalam class AuthController

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Laravel akan mengurus pembuatan token reset secara otomatis
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Link reset password telah dikirim ke email anda.'])
            : response()->json(['message' => 'Gagal mengirim email reset.'], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email tidak ditemukan.'], 404);
        }

        $tokenRow = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$tokenRow) {
            return response()->json(['message' => 'Token tidak valid atau sudah expired.'], 400);
        }

        if (!Hash::check($request->token, $tokenRow->token)) {
            return response()->json(['message' => 'Token tidak cocok.'], 400);
        }

        $createdAt = \Carbon\Carbon::parse($tokenRow->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json(['message' => 'Token sudah expired, silakan request ulang.'], 400);
        }

        // Gunakan plain text - cast 'hashed' di User model akan hash otomatis
    $user->password = $request->password;
    $user->remember_token = Str::random(60);
    $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil diubah.']);
    }
}
