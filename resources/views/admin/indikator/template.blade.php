@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-6">

        {{-- Pesan Flash --}}
        @if (session('success'))
            <div class=" text-green-700 px-4 py-3 rounded text-xs -ml-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="text-red-700 px-4 py-3 rounded text-xs -ml-4" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <h1 class="text-2xl font-bold mb-4">Template Indikator</h1>

        {{-- Form Salin Template --}}
        <form action="{{ route('indikator.template.copy') }}" method="POST" class="mb-6 bg-gray-50 p-4 rounded border">
            @csrf

            <div class="flex gap-4 items-end">
                <div>
                    <label for="old_periode_id" class="block text-sm font-medium text-gray-700 mb-1">Periode Sumber</label>
                    <select name="old_periode_id" id="old_periode_id" required
                        class="border rounded px-3 py-2 text-sm w-full">
                        <option value="">-- Pilih Periode Lama --</option>
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->tahun }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="new_periode_id" class="block text-sm font-medium text-gray-700 mb-1">Periode Tujuan</label>
                    <select name="new_periode_id" id="new_periode_id" required
                        class="border rounded px-3 py-2 text-sm w-full">
                        <option value="">-- Pilih Periode Baru --</option>
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->nama }} ({{ $p->tahun }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        Salin Template
                    </button>
                </div>
            </div>
        </form>

        {{-- Form Filter Indikator --}}
        <form action="{{ route('indikator.template') }}" method="GET"
            class="bg-white shadow p-4 rounded-lg mb-6 border border-gray-200">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                {{-- Periode --}}
                <div class="w-full md:w-1/3">
                    <label for="periode_id" class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <select name="periode_id" id="periode_id" class="w-full rounded border px-3 py-2 text-sm" required>
                        <option value="">-- Pilih Periode --</option>
                        @foreach ($periodes as $p)
                            <option value="{{ $p->id }}" {{ request('periode_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ $p->tahun }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kategori --}}
                <div class="w-full md:w-1/3">
                    <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori" id="kategori" class="w-full rounded border px-3 py-2 text-sm">
                        <option value="" {{ request('kategori') == '' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="pemenuhan" {{ request('kategori') == 'pemenuhan' ? 'selected' : '' }}>Pemenuhan
                        </option>
                        <option value="reform" {{ request('kategori') == 'reform' ? 'selected' : '' }}>Reform</option>
                    </select>
                </div>

                {{-- Tombol --}}
                <div class="md:mb-1">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        Lihat
                    </button>
                </div>
            </div>
        </form>

        {{-- Daftar Indikator --}}
        @if (request('periode_id'))
            <form action="{{ route('indikator.template.publish') }}" method="POST">
                @csrf
                <input type="hidden" name="periode_id" value="{{ request('periode_id') }}">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">
                        Indikator {{ request('kategori') == '' ? 'Semua Kategori' : ucfirst(request('kategori')) }} -
                        {{ $periode->nama }} ({{ $periode->tahun }})
                    </h2>
                </div>

                <div class="bg-white shadow-md rounded-lg overflow-x-auto p-4">
                    <table class="min-w-full table-auto text-sm text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-center"><input type="checkbox" id="checkAll"></th>
                                <th class="px-4 py-2">Kategori</th>
                                <th class="px-4 py-2">Area</th>
                                <th class="px-4 py-2">Sub Area</th>
                                <th class="px-4 py-2">Nama Indikator</th>
                                <th class="px-4 py-2">Pertanyaan</th>
                                <th class="px-4 py-2">Tipe Jawaban</th>
                                <th class="px-4 py-2 text-center">Status</th>
                                <th class="px-4 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($indikators as $indikator)
                                <tr class="border-t">
                                    <td class="px-4 py-2 text-center">
                                        <input type="checkbox" name="indikator_ids[]" value="{{ $indikator->id }}">
                                    </td>
                                    <td class="px-4 py-2">{{ ucfirst($indikator->kategori) }}</td>
                                    <td class="px-4 py-2">{{ $indikator->area->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $indikator->subArea->name ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $indikator->nama_indikator ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $indikator->pertanyaan }}</td>
                                    <td class="px-4 py-2">{{ strtoupper($indikator->tipe_jawaban) }}</td>
                                    <td class="px-4 py-2 text-center">
                                        @if ($indikator->publishedForPeriode(request('periode_id')))
                                            <span
                                                class="bg-green-100 text-green-800 px-2 py-1 text-xs rounded">Published</span>
                                        @else
                                            <span
                                                class="bg-yellow-100 text-yellow-800 px-2 py-1 text-xs rounded">Draft</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <div class="flex justify-center space-x-2">
                                            {{-- Edit --}}
                                            <a href="{{ route('indikator.edit', [
                                                'id' => $indikator->id,
                                                'from' => 'template',
                                                'periode_id' => request('periode_id'),
                                                'kategori' => request('kategori'),
                                            ]) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white text-xs py-1 px-3 rounded">
                                                Edit
                                            </a>

                                            {{-- Hapus --}}
                                            <button type="button" onclick="deleteIndikator('{{ $indikator->id }}')"
                                                class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1 rounded">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-gray-500">Belum ada indikator.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                    <div class="mt-4 mr-4 text-right">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-2 rounded text-sm">
                            Publish
                        </button>
                    </div>
                </div>
            </form>

            {{-- Form hapus global (dikirim lewat JS) --}}
            <form id="delete-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    {{-- Script Check All + Delete --}}
    <script>
        document.getElementById('checkAll')?.addEventListener('change', function(e) {
            document.querySelectorAll('input[name="indikator_ids[]"]').forEach(cb => cb.checked = e.target.checked);
        });

        function deleteIndikator(id) {
            if (!id) {
                alert('ID tidak valid!');
                return;
            }

            if (confirm('Yakin ingin menghapus indikator ini?')) {
                const form = document.getElementById('delete-form');
                form.action = `/admin/indikator/${id}`; 
                form.submit();
            }
        }
    </script>
@endsection
