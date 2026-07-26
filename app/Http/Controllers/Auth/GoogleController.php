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
            // Nonaktifkan verifikasi SSL untuk koneksi HTTP internal Socialite di Windows/Laragon
            $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
            $googleUser = Socialite::driver('google')->setHttpClient($guzzleClient)->user();

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
                    'password'  => bcrypt(Str::random(16)),
                    'role'      => 'user', // Role default untuk pembeli publik
                ]);
            } else {
                // Jika user sudah ada tetapi rolenya admin/organizer,
                // ubah rolenya jadi 'user' atau tetap izinkan login sebagai user pembeli publik
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar'    => $googleUser->avatar,
                ]);
            }

            // Logout dari guard admin/organizer jika ada session menggantung
            if (Auth::guard('admin')->check()) {
                Auth::guard('admin')->logout();
            }
            if (Auth::guard('organizer')->check()) {
                Auth::guard('organizer')->logout();
            }

            // 🔥 LOGIN KHUSUS GUARD 'WEB' (Pembeli Publik)
            Auth::guard('web')->login($user);
            session()->regenerate();

            return redirect()->route('home')->with('success', 'Selamat datang, ' . $user->name);
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Gagal melakukan login menggunakan Google: ' . $e->getMessage());
        }
    }

    /**
     * LOGOUT KHUSUS USER PUBLIK (Guard 'web')
     */
    public function logout()
    {
        Auth::guard('web')->logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }
} 