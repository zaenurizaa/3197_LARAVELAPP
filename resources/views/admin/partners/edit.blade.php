@extends('layout.admin')

@section('content')
<div class="p-6 max-w-xl mx-auto">
    <h1 class="text-2xl font-bold text-slate-800 mb-6">Ubah Data Partner</h1>

    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.partners.update', $partner->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">Nama Partner</label>
                <input type="text" name="name" value="{{ $partner->name }}" required 
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1">URL / Path Logo Partner</label>
                <input type="text" name="logo_url" value="{{ $partner->logo_url }}" 
                       class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-600">
            </div>

            <div class="flex gap-2 pt-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition">Simpan Perubahan</button>
<a href="{{ route('admin.partners.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-semibold hover:bg-slate-200 transition">Kembali</a>        </form>
    </div>
</div>
@endsection