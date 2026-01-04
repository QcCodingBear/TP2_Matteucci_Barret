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
        Schema::table('actor_film', function (Blueprint $table) {
            $table->foreignId('actor_id')->cascadeOnDelete()->change();
            $table->foreignId('film_id')->cascadeOnDelete()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
