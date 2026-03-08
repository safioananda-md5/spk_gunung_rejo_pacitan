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
        Schema::create('alternative_penerimaans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penerimaan_id');
            $table->foreign('penerimaan_id')
                ->references('id')->on('penerimaans')
                ->onDelete('cascade');
            $table->unsignedBigInteger('alternative_id');
            $table->foreign('alternative_id')
                ->references('id')->on('alternatives')
                ->onDelete('cascade');
            $table->integer('rank');
            $table->float('value');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alternative_penerimaans');
    }
};
