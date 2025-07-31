@extends('layouts.manager')

@section('content')
    <div class="max-w-full mx-auto px-4 py-6">
        <h1 class="text-xl font-bold mb-4">
            Lembar Kerja Evaluasi -
            @if ($periode)
                {{ $periode->nama }} ({{ $periode->tahun }})
            @else
                <span class="font-normal text-red-500 text-sm">Belum ada periode aktif</span>
            @endif
        </h1>
        <div class="mb-4">
            <form method="GET" action="{{ route('manager-area.indikator.index', [$area, $kategori]) }}">
                <div class="flex items-center gap-2">
                    <label for="periode_id" class="text-sm font-medium" style="color: #0E4A64;">
                        Pilih Periode Pengisian:
                    </label>
                    <select name="periode_id" id="periode_id" onchange="this.form.submit()"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring focus:ring-blue-200">
                        @foreach ($semuaPeriode as $p)
                            <option value="{{ $p->id }}"
                                {{ request('periode_id', $periode->id ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>


        @if (!$readonly && !$semuaSudahSubmit && $periode)
            <form action="{{ route('manager-area.jawaban.store') }}" method="POST">
                @csrf
                <input type="hidden" name="area_id" value="{{ $areaId }}">
                <input type="hidden" name="kategori" value="{{ $kategori }}">
                <input type="hidden" name="periode_id" value="{{ $periode->id }}">
        @endif

        <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
            <table class="min-w-full table-auto text-sm text-left">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-center">No</th>
                        <th class="border px-2 py-1 text-center">Nama Indikator</th>
                        <th class="border px-2 py-1 text-center">Pertanyaan</th>
                        <th class="border px-2 py-1 text-center">Bobot</th>
                        <th class="border px-2 py-1 text-center">Pilihan</th>
                        <th class="border px-2 py-1 text-center">Jawaban</th>
                        <th class="border px-2 py-1 text-center">Nilai</th>
                        <th class="border px-2 py-1 text-center">%</th>
                        <th class="border px-2 py-1 text-center">Catatan</th>
                        <th class="border px-2 py-1 text-center">Bukti</th>
                        <th class="border px-2 py-1 text-center">Link</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($indikators as $i => $indikator)
                        @php $jawab = $isianJawaban[$indikator->id] ?? []; @endphp
                        <tr class="border-t align-top">
                            <td class="border px-2 py-1">{{ $i + 1 }}</td>
                            <td class="border px-2 py-1">{{ $indikator->nama_indikator }}</td>
                            <td class="border px-2 py-1">{{ $indikator->pertanyaan }}</td>
                            <td class="border px-2 py-1 text-center">{{ number_format($indikator->bobot, 2) }}</td>
                            <td class="border px-2 py-1">
                                @if ($indikator->tipe_jawaban === 'esai')
                                    <div>Esai</div>
                                @else
                                    @foreach ($indikator->opsiJawaban as $opsi)
                                        <div>{{ $opsi->opsi }}. {{ $opsi->teks }}</div>
                                    @endforeach
                                @endif
                            </td>
                            <td class="border px-2 py-1">
                                @if ($indikator->tipe_jawaban === 'ya/tidak')
                                    <select name="jawaban[{{ $indikator->id }}]"
                                        class="w-full border rounded px-2 py-1 text-sm jawaban-select"
                                        data-id="{{ $indikator->id }}" data-jenis="ya_tidak"
                                        {{ $jawab['is_disabled'] ?? '' }}>
                                        <option value="">-- Pilih --</option>
                                        <option value="Ya" data-bobot="1.00"
                                            {{ ($jawab['jawaban'] ?? '') === 'Ya' ? 'selected' : '' }}>Ya</option>
                                        <option value="Tidak" data-bobot="0.00"
                                            {{ ($jawab['jawaban'] ?? '') === 'Tidak' ? 'selected' : '' }}>Tidak</option>
                                    </select>
                                @elseif ($indikator->tipe_jawaban === 'abcde')
                                    <select name="jawaban[{{ $indikator->id }}]"
                                        class="w-full border rounded px-2 py-1 text-sm jawaban-select"
                                        data-id="{{ $indikator->id }}" data-jenis="abcde"
                                        {{ $jawab['is_disabled'] ?? '' }}>
                                        <option value="">-- Pilih --</option>
                                        @foreach ($indikator->opsiJawaban as $opsi)
                                            <option value="{{ $opsi->opsi }}" data-bobot="{{ $opsi->bobot }}"
                                                {{ ($jawab['jawaban'] ?? '') === $opsi->opsi ? 'selected' : '' }}>
                                                {{ $opsi->opsi }}. {{ $opsi->teks }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="number" step="0.01" min="0" max="100"
                                        name="jawaban[{{ $indikator->id }}]"
                                        class="w-full border rounded px-2 py-1 text-sm angka-input"
                                        data-id="{{ $indikator->id }}" value="{{ $jawab['jawaban'] ?? '' }}"
                                        {{ $jawab['is_disabled'] ?? '' }}>
                                @endif
                            </td>
                            <td class="border px-2 py-1 text-center">
                                <input type="text" name="nilai[{{ $indikator->id }}]"
                                    class="w-16 border rounded px-2 py-1 text-sm" readonly
                                    value="{{ $jawab['nilai'] ?? '' }}">
                            </td>
                            <td class="border px-2 py-1 text-center">
                                <input type="text" name="persen[{{ $indikator->id }}]"
                                    class="w-16 border rounded px-2 py-1 text-sm" readonly
                                    value="{{ $jawab['persen'] ?? '' }}">
                            </td>
                            <td class="border px-2 py-1">
                                <textarea name="catatan[{{ $indikator->id }}]" rows="2" class="w-48 border rounded px-2 py-1 text-sm"
                                    {{ $jawab['is_disabled'] ?? '' }}>{{ $jawab['catatan'] ?? '' }}</textarea>
                            </td>
                            <td class="border px-2 py-1">
                                <input type="text" name="bukti[{{ $indikator->id }}]" required
                                    class="w-48 border rounded px-2 py-1 text-sm" value="{{ $jawab['bukti'] ?? '' }}"
                                    {{ $jawab['is_disabled'] ?? '' }}>
                            </td>
                            <td class="border px-2 py-1">
                                <input type="url" name="link[{{ $indikator->id }}]" required
                                    class="w-48 border rounded px-2 py-1 text-sm" value="{{ $jawab['link'] ?? '' }}"
                                    {{ $jawab['is_disabled'] ?? '' }}>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-gray-500 italic py-4">
                                Indikator untuk periode ini belum dipublish oleh admin.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        @if (!$readonly && !$semuaSudahSubmit && $periode)
            <div class="mt-4 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm">
                    Simpan Jawaban
                </button>
            </div>
            </form>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('.jawaban-select');
            const angkaInputs = document.querySelectorAll('.angka-input');

            function hitungNilai(id, bobot) {
                const nilaiInput = document.querySelector(`input[name="nilai[${id}]"]`);
                const persenInput = document.querySelector(`input[name="persen[${id}]"]`);

                if (nilaiInput && persenInput) {
                    nilaiInput.value = parseFloat(bobot).toFixed(2);
                    persenInput.value = (parseFloat(bobot) * 100).toFixed(0);
                }
            }

            selects.forEach(select => {
                select.addEventListener('change', function() {
                    const id = this.dataset.id;
                    const selectedOption = this.options[this.selectedIndex];
                    const bobot = selectedOption.getAttribute('data-bobot') || 0;
                    hitungNilai(id, bobot);
                });
            });

            angkaInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const id = this.dataset.id;
                    const nilai = parseFloat(this.value) || 0;
                    hitungNilai(id, nilai / 100);
                });
            });
        });
    </script>

@endsection
