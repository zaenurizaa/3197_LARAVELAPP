<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource (READ).
     */
    public function index()
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $isSuperAdmin = $user ? $user->isSuperAdmin() : false;

        if ($isSuperAdmin) {
            // Superadmin melihat semua event
            $events = Event::with(['category', 'tenant'])->latest()->paginate(10);
        } else {
            // Organizer hanya melihat event miliknya sendiri
            $events = Event::with(['category', 'tenant'])
                ->where('tenant_id', $user->tenant_id)
                ->latest()
                ->paginate(10);
        }
        
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource (CREATE).
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage (STORE).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|numeric|min:1',
            'poster'      => 'nullable|image|max:2048'
        ]);

        // 🔥 1. ISI TENANT_ID DAN USER_ID SECARA OTOMATIS
        $user = Auth::user();
        $data['user_id']   = $user->id;
        $data['tenant_id'] = $user->tenant_id; // Mengambil tenant_id milik user yang sedang buat event

        // 🔥 2. BUAT SLUG DARI TITLE
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);

        // 🔥 3. OPSI CLOUDINARY UPLOAD / URL IMAGE INPUT
        $imageUrl = null;
        if ($request->hasFile('poster')) {
            // Upload ke Cloudinary secara langsung
            $imageUrl = $this->uploadToCloudinary($request->file('poster'));
        } elseif ($request->filled('poster_url')) {
            // Jika user memasukkan URL gambar dari internet langsung
            $imageUrl = $request->input('poster_url');
        }

        $data['poster_path'] = $imageUrl;

        $event = Event::create($data);

        // 🔥 SIMPAN EVENT TIERS DINAMIS
        if ($request->has('tiers')) {
            foreach ($request->tiers as $tierData) {
                if (!empty($tierData['name'])) {
                    $event->tiers()->create([
                        'name'       => $tierData['name'],
                        'price'      => $tierData['price'] ?? 0,
                        'stock'      => $tierData['stock'] ?? 0,
                        'start_date' => $tierData['start_date'] ? \Carbon\Carbon::parse($tierData['start_date']) : null,
                        'end_date'   => $tierData['end_date'] ? \Carbon\Carbon::parse($tierData['end_date']) : null,
                    ]);
                }
            }
        }

        // 🔥 DYNAMIC REDIRECT BERDASARKAN ROLE LOGIN
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $redirectRoute = $user->isSuperAdmin() ? 'admin.events.index' : 'organizer.events.index';

        return redirect()->route($redirectRoute)->with('success', 'Data Event berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        if (!$user->isSuperAdmin() && $event->tenant_id !== $user->tenant_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengedit event ini.');
        }

        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified resource in storage (UPDATE).
     */
    public function update(Request $request, Event $event)
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        if (!$user->isSuperAdmin() && $event->tenant_id !== $user->tenant_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah event ini.');
        }

        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'date'        => 'required|date',
            'location'    => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|numeric|min:1',
            'poster'      => 'nullable|image|max:2048',
            'poster_url'  => 'nullable|url'
        ]);

        // Update slug jika judul berubah
        if ($event->title !== $request->title) {
            $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        }

        if ($request->hasFile('poster')) {
            // Upload yang baru ke Cloudinary
            $data['poster_path'] = $this->uploadToCloudinary($request->file('poster'));
        } elseif ($request->filled('poster_url')) {
            $data['poster_path'] = $request->input('poster_url');
        }

        $event->update($data);

        // 🔥 UPDATE EVENT TIERS DINAMIS
        if ($request->has('tiers')) {
            // Hapus tier lama untuk menulis ulang yang baru (sederhana & aman dari error)
            $event->tiers()->delete();
            
            foreach ($request->tiers as $tierData) {
                if (!empty($tierData['name'])) {
                    $event->tiers()->create([
                        'name'       => $tierData['name'],
                        'price'      => $tierData['price'] ?? 0,
                        'stock'      => $tierData['stock'] ?? 0,
                        'start_date' => $tierData['start_date'] ? \Carbon\Carbon::parse($tierData['start_date']) : null,
                        'end_date'   => $tierData['end_date'] ? \Carbon\Carbon::parse($tierData['end_date']) : null,
                    ]);
                }
            }
        }

        // 🔥 DYNAMIC REDIRECT BERDASARKAN ROLE LOGIN
        $redirectRoute = $user->isSuperAdmin() ? 'admin.events.index' : 'organizer.events.index';
        return redirect()->route($redirectRoute)->with('success', 'Rincian data event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (DELETE).
     */
    public function destroy(Event $event)
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        if (!$user->isSuperAdmin() && $event->tenant_id !== $user->tenant_id) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus event ini.');
        }

        $event->delete();

        // 🔥 DYNAMIC REDIRECT BERDASARKAN ROLE LOGIN PASCA HAPUS
        $user = Auth::guard('admin')->user() ?? Auth::guard('organizer')->user() ?? auth()->user();
        $redirectRoute = $user->isSuperAdmin() ? 'admin.events.index' : 'organizer.events.index';

        return redirect()->route($redirectRoute)->with('success', 'Data event berhasil dihapus secara permanen.');
    }

    /**
     * Helper Upload File ke Cloudinary menggunakan HTTP Client Native Laravel (Vercel-Friendly)
     */
    private function uploadToCloudinary($file): ?string
    {
        try {
            $cloudName = env('CLOUDINARY_CLOUD_NAME', 'xjcuffm0');
            $apiKey    = env('CLOUDINARY_API_KEY', '181785848684867');
            $apiSecret = env('CLOUDINARY_API_SECRET', 'Kb_dciSPVPa4Z7bpAvb0ZWlJ--c');

            // Kita pakai API Unsigned Upload Presets Cloudinary agar praktis
            // atau API Signature standard jika Anda ingin upload aman
            $timestamp = time();
            $signature = sha1("timestamp={$timestamp}{$apiSecret}");

            $response = \Illuminate\Support\Facades\Http::attach(
                'file', 
                file_get_contents($file->getRealPath()), 
                $file->getClientOriginalName()
            )->post("https://api.cloudinary.com/v1_1/{$cloudName}/image/upload", [
                'api_key'   => $apiKey,
                'timestamp' => $timestamp,
                'signature' => $signature
            ]);

            if ($response->successful()) {
                return $response->json('secure_url');
            }

            \Illuminate\Support\Facades\Log::error('Cloudinary API Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal upload poster ke Cloudinary: ' . $e->getMessage());
            return null;
        }
    }
}