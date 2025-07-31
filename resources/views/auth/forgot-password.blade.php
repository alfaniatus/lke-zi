@extends('layouts.login')

@section('title', 'Lupa Password')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Lupa Password</h1>

    @if (session('status'))
        <div class="text-green-600 mb-2 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label for="email" class="block text-sm mb-1">Masukkan Email Anda</label>
        <input type="email" name="email" id="email" required
               class="w-full px-3 py-2 border rounded focus:outline-none focus:ring">

        <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded">
            Kirim Link Reset
        </button>
    </form>
</div>
@endsection
