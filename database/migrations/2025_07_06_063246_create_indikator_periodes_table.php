<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indikator_periode', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('indikator_id');
            $table->unsignedBigInteger('periode_id');
            $table->boolean('published')->default(false);
            $table->timestamps();

            $table->unique(['indikator_id', 'periode_id']);

            $table->foreign('indikator_id')
                ->references('id')->on('indikators')
                ->onDelete('cascade');

            $table->foreign('periode_id')
                ->references('id')->on('periodes')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikator_periode');
    }
};

