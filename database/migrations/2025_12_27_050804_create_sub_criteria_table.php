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
        Schema::create('sub_criteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('criteria_id');
            $table->foreign('criteria_id')
                ->references('id')->on('criterias')
                ->onDelete('cascade');
            $table->float('scale');
            $table->float('upper_value')->nullable();
            $table->float('under_value')->nullable();
            $table->float('initial_value')->nullable();
            $table->float('final_value')->nullable();
            $table->string('sameas_value')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_criteria');
    }
};
