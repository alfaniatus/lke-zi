@extends('layouts.login')

@section('title', 'Reset Password')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Reset Password</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ request('email') }}">

        <label class="block text-sm mb-1">Password Baru</label>
        <input type="password" name="password" required
               class="w-full px-3 py-2 border rounded mb-3">

        <label class="block text-sm mb-1">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" required
               class="w-full px-3 py-2 border rounded">

        <button type="submit" class="mt-4 w-full bg-blue-600 text-white py-2 rounded">
            Simpan Password Baru
        </button>
    </form>
</div>
@endsection
