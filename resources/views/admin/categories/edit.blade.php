@extends('layout.admin')

@section('title', 'Edit Kategori')
@section('page_title', 'Edit Kategori')
@section('page_subtitle', 'Ubah rincian informasi kategori terpilih.')

@section('content')
<div class="max-w-2xl bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
        <h2 class="text-xl font-bold text-slate-800">Form Mengubah Nama Kategori</h2>
    </div>
    
    <div class="p-6">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-600">Nama Kategori</label>
                <input type="text" name="name" value="{{ $category->name }}" required placeholder="Ubah nama kategori..."
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600 transition bg-white font-medium">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-100 text-sm">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200 transition text-sm">
                    Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection