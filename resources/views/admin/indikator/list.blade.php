@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-white rounded shadow text-sm">
        <h2 class="text-xl font-bold mb-4">Daftar Indikator Belum Dipublish</h2>

        @if (session('success'))
            <div class="mb-4 text-green-600">{{ session('success') }}</div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('indikator.create') }}"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                + Tambah Indikator
            </a>
        </div>

        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-4 py-2">Area</th>
                    <th class="border px-4 py-2">Sub Area</th>
                    <th class="border px-4 py-2">Kategori</th>
                    <th class="border px-4 py-2">Nama Indikator</th>
                    <th class="border px-4 py-2">Pertanyaan</th>
                    <th class="border px-4 py-2">Tipe Jawaban</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($indikators as $indikator)
                    <tr>
                        <td class="border px-4 py-2">{{ $indikator->area->name }}</td>
                        <td class="border px-4 py-2">{{ $indikator->subArea->name }}</td>
                        <td class="border px-4 py-2">{{ ucfirst($indikator->kategori) }}</td>
                        <td class="border px-4 py-2">{{ $indikator->nama_indikator }}</td>
                        <td class="border px-4 py-2">{{ $indikator->pertanyaan }}</td>
                        <td class="border px-4 py-2">{{ $indikator->tipe_jawaban }}</td>
                        <td class="border px-4 py-2 text-center">
                            @if ($indikator->status !== 'published')
                                <form action="{{ route('indikator.toggle-publish', $indikator->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded">
                                        Publish
                                    </button>
                                </form>
                            @else
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">
                                    Published
                                </span>
                            @endif
                        </td>
                        <td class="border px-4 py-2">
                            <div class="flex justify-center space-x-2">
                                {{-- Tombol Edit --}}
                                <a href="{{ route('indikator.edit', $indikator->id) }}"
                                    class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded">
                                    Edit
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('indikator.destroy', $indikator->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus indikator ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
