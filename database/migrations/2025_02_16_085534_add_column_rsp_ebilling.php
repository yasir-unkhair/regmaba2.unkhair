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
        Schema::table('app_peserta_has_pembayaran', function (Blueprint $table) {
            $table->json('rsp_ebilling')->nullable()->after('tgl_pelunasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_peserta_has_pembayaran', function (Blueprint $table) {
            $table->dropColumn('rsp_ebilling');
        });
    }
};
