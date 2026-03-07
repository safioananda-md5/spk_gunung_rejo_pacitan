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
        Schema::table('alternatives', function (Blueprint $table) {
            // Menambah kolom baru
            $table->float('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alternatives', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('value');
        });
    }
};
