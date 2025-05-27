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
        Schema::table('app_peserta', function (Blueprint $table) {
            $table->boolean('rsp_ebilling')->default(0)->comment('kirim npm ke e-billing setelah generate')->after('tgl_generate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_peserta', function (Blueprint $table) {
            $table->dropColumn('rsp_ebilling');
        });
    }
};
