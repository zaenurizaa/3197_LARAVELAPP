@extends('layout.admin')

@section('content')
<div class="container">
    <h2>Daftar Partner</h2>
    
    <a href="#formTambah" class="btn btn-primary mb-3">Tambah Partner</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Partner</th>
                <th>Logo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($partners as $partner)
            <tr>
                <td>{{ $partner->id }}</td>
                <td>{{ $partner->name }}</td>
                <td><img src="{{ $partner->logo_url }}" alt="logo" width="50"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div id="formTambah" class="mt-5">
    <h3>Tambah Partner Baru</h3>
    <form action="{{ route('partners.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nama Partner</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Logo URL</label>
            <input type="text" name="logo_url" class="form-control" value="https://placehold.co/200x200" required>
        </div>
        <button type="submit" class="btn btn-success mt-2">Simpan Partner</button>
    </form>
</div>
</div>
@endsection