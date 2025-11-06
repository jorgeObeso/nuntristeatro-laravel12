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
        Schema::create('idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('idioma', 45);
            $table->char('codigo', 2);
            $table->char('label', 3)->nullable();
            $table->string('imagen', 100)->nullable();
            $table->boolean('principal')->default(false);
            $table->boolean('activado')->default(true);
            $table->timestamps();
            
            $table->unique('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idiomas');
    }
};
