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
        Schema::table('films', function (Blueprint $table) {
            $table->string('image', 40)->nullable()->change();
        });

        //source: https://stackoverflow.com/questions/74299841/how-to-make-function-ondelete-cascade-in-laravel-delete-function
        Schema::table('critics', function (Blueprint $table) {
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
