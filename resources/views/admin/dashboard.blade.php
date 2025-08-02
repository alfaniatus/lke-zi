@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="text-2xl font-bold mb-4">Rekap Penilaian Semua Area</h1>

    <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-4">
        <label for="periode_id" class="text-sm font-medium text-gray-700">Pilih Periode:</label>
        <select name="periode_id" id="periode_id" class="ml-2 border rounded p-1" onchange="this.form.submit()">
            @foreach ($periodes as $periode)
                <option value="{{ $periode->id }}" {{ $periode->id == $periodeId ? 'selected' : '' }}>
                    {{ $periode->nama }}
                </option>
            @endforeach
        </select>
    </form>

    @foreach ($rekap as $area => $kategoriData)
        <div class="mb-6">
            <h2 class="text-xl font-semibold mb-2">Area: {{ $area }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">Kategori</th>
                            <th class="border px-4 py-2">Total Bobot</th>
                            <th class="border px-4 py-2">Total Nilai</th>
                            <th class="border px-4 py-2">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kategoriData as $kategori => $data)
                            <tr>
                                <td class="border px-4 py-2 capitalize">{{ $kategori }}</td>
                                <td class="border px-4 py-2">{{ number_format($data['total_bobot'], 2) }}</td>
                                <td class="border px-4 py-2">{{ number_format($data['total_nilai'], 2) }}</td>
                                <td class="border px-4 py-2">{{ $data['persen'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection
