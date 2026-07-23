<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman login Google
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback data setelah pengguna berhasil login dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // 🔥 CLEAR SESSION ADMIN JIKA ADA: Bersihkan session lama agar tidak saling ganggu
            if (Auth::check()) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
            }

            // Cari apakah email/google_id ini sudah terdaftar di database
            $user = User::where('google_id', $googleUser->id)
                        ->orWhere('email', $googleUser->email)
                        ->first();

            if (!$user) {
                // Jika belum terdaftar, buat user baru secara instan sebagai 'user' publik
                $user = User::create([
                    'name'      => $googleUser->name,
                    'email'     => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                    'password'  => bcrypt(Str::random(16)), // Password acak yang aman
                    'role'      => 'user', // Pastikan role publik
                ]);
            } else {
                // Jika sudah ada, cukup update Google ID dan fotonya jika berubah
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar
                ]);
            }

            // 🔥 PROTEKSI ROLE ADMIN: Tolak jika email ini punya role admin/organizer via SSO Publik
            if (in_array($user->role, ['admin', 'organizer'])) {
                return redirect()->route('admin.login')->with('error', 'Akun Pengelola wajib login melalui portal Login Admin resmi.');
            }

            // Login-kan user ke sistem Laravel
            Auth::login($user);
            session()->regenerate();
            
            return redirect()->route('home')->with('success', 'Selamat datang, ' . $user->name);
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Gagal melakukan login menggunakan Google: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 LOGOUT KHUSUS USER PUBLIK: Dilempar TEGAS ke Beranda Web Utama (Bukan Login Admin)
     */
    public function logout()
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }
}