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
        if (!Schema::hasColumns('app_peserta_has_pembayaran', ['kategori_ukt'])) {
            Schema::table('app_peserta_has_pembayaran', function (Blueprint $table) {
                $table->char('kategori_ukt', 10)->nullable()->after('jenis_pembayaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumns('app_peserta_has_pembayaran', ['kategori_ukt'])) {
            Schema::table('app_peserta_has_pembayaran', function (Blueprint $table) {
                $table->dropColumn('kategori_ukt');
            });
        }
    }
};
