@extends('layouts.manager')

@section('content')
    <div class="max-w-full mx-auto px-4 py-6 text-sm">
        <h1 class="text-xl font-bold mb-4">
            Rekap Jawaban - {{ $periode->nama ?? 'Periode' }} ({{ $periode->tahun ?? '' }})
        </h1>

        <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
            <table class="min-w-full table-auto border text-left text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-2 py-1 border">No</th>
                        <th class="px-2 py-1 border">Nama Indikator</th>
                        <th class="px-2 py-1 border">Pertanyaan</th>
                        <th class="px-2 py-1 border">Bobot</th>
                        <th class="px-2 py-1 border">Jawaban</th>
                        <th class="px-2 py-1 border text-center">Nilai</th>
                        <th class="px-2 py-1 border text-center">%</th>
                        <th class="px-2 py-1 border">Catatan</th>
                        <th class="px-2 py-1 border">Bukti</th>
                        <th class="px-2 py-1 border">Link</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jawabans as $i => $jawaban)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-2 py-1 border text-center">{{ $i + 1 }}</td>
                            <td class="px-2 py-1 border">{{ $jawaban->indikator->nama_indikator ?? '-' }}</td>
                            <td class="px-2 py-1 border">{{ $jawaban->indikator->pertanyaan ?? '-' }}</td>
                            <td class="px-2 py-1 border text-center">{{ number_format($jawaban->indikator->bobot ?? 0, 2) }}
                            </td>
                            <td class="px-2 py-1 border text-center">{{ $jawaban->jawaban ?? '-' }}</td>
                            <td class="px-2 py-1 border text-center">{{ number_format($jawaban->nilai ?? 0, 2) }}</td>
                            <td class="px-2 py-1 border text-center">{{ number_format($jawaban->persen ?? 0, 0) }}%</td>
                            <td class="px-2 py-1 border">{{ $jawaban->catatan ?? '-' }}</td>
                            <td class="px-2 py-1 border">{{ $jawaban->bukti ?? '-' }}</td>
                            <td class="px-2 py-1 border">
                                @if ($jawaban->link)
                                    <a href="{{ $jawaban->link }}" target="_blank" class="text-blue-600 underline">Link</a>
                                @else
                                    <em>-</em>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-gray-500">Belum ada jawaban disimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

           <div class="mt-6 text-right">
    @if ($sudahDikirim)
        {{-- Jika sudah submit, tampilkan pesan sukses --}}
        @if(session('success'))
            <p class="text-green-600 font-medium mb-2">
                 {{ session('success') }}
            </p>
        @endif
    @else
        {{-- Jika belum submit, tampilkan tombol --}}
        <form method="POST" action="{{ route('manager-area.submit-jawaban', ['area' => $areaId, 'kategori' => $currentKategori]) }}">
            @csrf
            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
            <button type="submit"
                onclick="return confirm('Anda yakin ingin mengirim jawaban ini?')"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                Submit Jawaban
            </button>
        </form>
    @endif
</div>


        </div>
    </div>
@endsection
