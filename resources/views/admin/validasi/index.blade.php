@extends('layouts.app')

@section('content')
    <div class="max-w-full mx-auto px-4 py-6 text-sm">
        <h1 class="text-xl font-bold mb-4">Validasi Jawaban Indikator</h1>

        @if (session('success'))
            <p class="text-green-600 text-sm mb-3">
                {{ session('success') }}
            </p>
        @endif
        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.validasi.index') }}" class="mb-4 flex flex-wrap gap-4 items-center">
            <div>
                <label for="periode_id" class="mr-2 font-semibold">Periode:</label>
                <select name="periode_id" id="periode_id" class="border rounded px-2 py-1 text-sm">
                    <option value="">-- Pilih Periode --</option>
                    @foreach ($periodes as $p)
                        <option value="{{ $p->id }}" @if (request('periode_id') == $p->id) selected @endif>
                            {{ $p->nama }} ({{ $p->tahun }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="area_id" class="mr-2 font-semibold">Area:</label>
                <select name="area_id" id="area_id" class="border rounded px-2 py-1 text-sm">
                    <option value="">-- Pilih Area --</option>
                    @foreach ($areas as $a)
                        <option value="{{ $a->id }}" @if (request('area_id') == $a->id) selected @endif>
                            {{ $a->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="kategori" class="mr-2 font-semibold">Kategori:</label>
                <select name="kategori" id="kategori" class="border rounded px-2 py-1 text-sm">
                    <option disabled selected hidden>-- Pilih Kategori--</option>
                    <option value="">Semua Kategori</option>
                    <option value="pemenuhan" @if (request('kategori') == 'pemenuhan') selected @endif>Pemenuhan</option>
                    <option value="reform" @if (request('kategori') == 'reform') selected @endif>Reform</option>
                </select>

            </div>

            <div>
                <label for="status_validasi" class="mr-2 font-semibold">Status Validasi:</label>
                <select name="status_validasi" id="status_validasi" class="border rounded px-2 py-1 text-sm">
                    <option value="">-- Status Validasi --</option>
                    <option value="pending" {{ request('status_validasi') === 'pending' ? 'selected' : '' }}>Pending
                    </option>
                    <option value="diterima" {{ request('status_validasi') === 'diterima' ? 'selected' : '' }}>Diterima
                    </option>
                    <option value="ditolak" {{ request('status_validasi') === 'ditolak' ? 'selected' : '' }}>Ditolak
                    </option>
                </select>
            </div>


            <button type="submit"
                class="ml-2 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">Tampilkan</button>
        </form>

        {{-- Tabel --}}
        <form method="POST" action="{{ route('admin.validasi.simpan') }}">
            @csrf
            <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
                <table class="min-w-full table-auto border text-left text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-2 py-1 border">No</th>
                            <th class="px-2 py-1 border">Indikator</th>
                            <th class="px-2 py-1 border">Pertanyaan</th>
                            <th class="px-2 py-1 border">Pilihan</th>
                            <th class="px-2 py-1 border text-center">Jawaban</th>
                            <th class="px-2 py-1 border text-center">Nilai</th>
                            <th class="px-2 py-1 border text-center">%</th>
                            <th class="px-2 py-1 border">Catatan</th>
                            <th class="px-2 py-1 border">Bukti</th>
                            <th class="px-2 py-1 border">Link</th>
                            <th class="px-2 py-1 border text-center">Validasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jawabans as $i => $jawaban)
                            <tr class="border-t hover:bg-gray-50 align-top">
                                <td class="px-2 py-1 border text-center">{{ $i + 1 }}</td>
                                <td class="px-2 py-1 border">{{ $jawaban->indikator->nama_indikator }}</td>
                                <td class="px-2 py-1 border">{{ $jawaban->indikator->pertanyaan }}</td>
                                <td class="px-2 py-1 border">
                                    <ul class="list-disc pl-4">
                                        @foreach ($jawaban->indikator->opsiJawaban as $opsi)
                                            <li>{{ $opsi->opsi }}. {{ $opsi->teks }}
                                                ({{ number_format($opsi->bobot, 2) }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-2 py-1 border text-center">{{ $jawaban->jawaban }}</td>
                                <td class="px-2 py-1 border text-center">{{ number_format($jawaban->nilai, 2) }}</td>
                                <td class="px-2 py-1 border text-center">{{ number_format($jawaban->persen, 0) }}%</td>
                                <td class="px-2 py-1 border">{{ $jawaban->catatan }}</td>
                                <td class="px-2 py-1 border">{{ $jawaban->bukti }}</td>
                                <td class="px-2 py-1 border">
                                    @if ($jawaban->link)
                                        <a href="{{ $jawaban->link }}" target="_blank"
                                            class="text-blue-600 underline">Link</a>
                                    @else
                                        <em class="text-gray-400">-</em>
                                    @endif
                                </td>
                                <td class="px-2 py-1 border text-center">
                                    <select class="status-select border rounded px-2 py-1 text-sm"
                                        name="validasi[{{ $jawaban->id }}][status]" data-id="{{ $jawaban->id }}">
                                        <option value="pending" @if ($jawaban->status_validasi === 'pending') selected @endif>Pending
                                        </option>
                                        <option value="diterima" @if ($jawaban->status_validasi === 'diterima') selected @endif>Terima
                                        </option>
                                        <option value="ditolak" @if ($jawaban->status_validasi === 'ditolak') selected @endif>Tolak
                                        </option>
                                    </select>

                                    <textarea
                                        class="catatan-field border rounded px-2 py-1 text-sm mt-1 w-full {{ $jawaban->status_validasi === 'ditolak' ? '' : 'hidden' }}"
                                        name="validasi[{{ $jawaban->id }}][catatan]" data-id="{{ $jawaban->id }}" placeholder="Tambahkan Catatan">{{ $jawaban->catatan_admin }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4 text-gray-500">Belum ada jawaban untuk validasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-right">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                    Validasi Semua Data
                </button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('.status-select').forEach(select => {
            const id = select.dataset.id;
            const textarea = document.querySelector(`.catatan-field[data-id="${id}"]`);

            if (select.value === 'ditolak') {
                textarea.classList.remove('hidden');
                textarea.required = true;
            }

            select.addEventListener('change', () => {
                if (select.value === 'ditolak') {
                    textarea.classList.remove('hidden');
                    textarea.required = true;
                } else {
                    textarea.classList.add('hidden');
                    textarea.required = false;
                }
            });
        });
    </script>
@endsection
