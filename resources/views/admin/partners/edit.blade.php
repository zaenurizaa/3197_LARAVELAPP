@extends('layout.admin')

@section('content')
<div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
    <h2 class="text-xl font-bold mb-6">Edit Partner: {{ $partner->name }}</h2>
    <form action="{{ route('partners.update', $partner->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm font-bold text-slate-600">Nama Partner</label>
            <input type="text" name="name" value="{{ $partner->name }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200">
        </div>
        <div>
            <label class="block text-sm font-bold text-slate-600">Logo URL</label>
            <input type="text" name="logo_url" value="{{ $partner->logo_url }}" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold">Update</button>
            <a href="{{ route('partners.index') }}" class="bg-slate-200 text-slate-700 px-6 py-3 rounded-xl font-bold">Batal</a>
        </div>
    </form>
</div>
@endsection