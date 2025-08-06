<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usulan_rkp', function (Blueprint $table) {
            $table->id();
            $table->string('bidang');
            $table->string('jenis_kegiatan');
            $table->string('lokasi');
            $table->string('volume');
            $table->string('perkiraan_waktu_pelaksanaan');
            $table->decimal('prakiraan_biaya_jumlah', 15, 2);

            $table->foreignId('sumber_pembiayaan_id')
                ->constrained('sumber_pembiayaans')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usulan_rkp');
    }
};
