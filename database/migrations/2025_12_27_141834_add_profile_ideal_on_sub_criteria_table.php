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
        Schema::table('sub_criteria', function (Blueprint $table) {
            // Menambah kolom baru
            $table->boolean('profile_ideal')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_criteria', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('profile_ideal');
        });
    }
};
