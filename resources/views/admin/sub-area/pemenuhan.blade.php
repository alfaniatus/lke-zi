@extends('layouts.app')
@section('title', 'Pemenuhan Area')

@section('content')
    <h1 class="text-xl font-bold mb-4">Progress Pengisian Pemenuhan</h1>

    <div class="bg-white p-4 rounded-xl shadow-md w-full md:w-1/2 mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm font-bold text-[#0E4A64]">
                {{ $jawabanTerisi }}/{{ $totalIndikator }}
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <div class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">
                ✓
            </div>

            <div class="flex-1 h-4 bg-gray-300 rounded-full overflow-hidden">
                <div class="h-4 bg-green-500 rounded-full transition-all duration-700 ease-in-out"
                    style="width: {{ $progressWidth }};"></div>
            </div>
        </div>
        <div class="text-xs text-gray-600 mt-1 text-right">
            {{ $progressWidth }}
        </div>
    </div>
@endsection
