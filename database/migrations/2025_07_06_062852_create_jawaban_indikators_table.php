<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jawaban_indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('indikator_id')->constrained()->onDelete('cascade');
            $table->foreignId('periode_id')->constrained()->onDelete('cascade');
            $table->string('jawaban')->nullable();
            $table->decimal('nilai', 5, 2)->default(0);
            $table->decimal('persen', 5, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->string('bukti')->nullable();
            $table->text('link')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->string('status')->nullable()->default('draft');
            $table
                ->enum('status_validasi', ['pending', 'diterima', 'ditolak'])
                ->nullable()
                ->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->boolean('submit_ulang')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_indikators');
    }
};
