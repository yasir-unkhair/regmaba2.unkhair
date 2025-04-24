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
        Schema::table('app_peserta_has_verifikasiberkas', function (Blueprint $table) {
            $table->text('catatan')->nullable()->comment('catatan verifikator')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_peserta_has_verifikasiberkas', function (Blueprint $table) {
            $table->string('catatan')->nullable()->comment('catatan verifikator');
        });
    }
};
