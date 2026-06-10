<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource (READ).
     */
    public function index()
    {
        $events = Event::with('category')->latest()->paginate(10);
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
        // Berdasarkan Instruksi Modul 9.4.3 (Validasi Diperketat)
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id', // Memastikan ID kategori benar-benar ada di DB
            'title' => 'required|string|max:255',
            'description' => 'nullable|string', // Diubah ke nullable sesuai modul agar fleksibel
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', // Ditambahkan min:0 untuk mencegah input minus (-5)
            'stock' => 'required|numeric|min:1', // Ditambahkan min:1 agar stok masuk akal
            'poster' => 'nullable|image|max:2048' // Memastikan file berupa gambar & max 2MB
        ]);

        if ($request->hasFile('poster')) {
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Data Event berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Update the specified resource in storage (UPDATE).
     */
    public function update(Request $request, Event $event)
    {
        // Berdasarkan Instruksi Modul 9.4.4
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0', // Validasi diperketat saat update
            'stock' => 'required|numeric|min:1',
            'poster' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('poster')) {
            // Hapus gambar lama jika sebelumnya sudah memiliki poster (Efisiensi storage server)
            if ($event->poster_path) {
                Storage::disk('public')->delete($event->poster_path);
            }
            $data['poster_path'] = $request->file('poster')->store('posters', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Rincian data event berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage (DELETE).
     */
    public function destroy(Event $event)
    {
        // Logika hapus file fisik milikmu sudah sangat tepat & aman!
        if ($event->poster_path) {
            Storage::disk('public')->delete($event->poster_path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Data event berhasil dihapus secara permanen.');
    }
}