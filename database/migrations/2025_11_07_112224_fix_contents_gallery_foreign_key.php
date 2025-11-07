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
        Schema::table('contents', function (Blueprint $table) {
            // Eliminar la clave foránea existente que referencia 'galerias'
            $table->dropForeign(['galeria_id']);
            
            // Modificar el enum para incluir 'galeria'
            $table->enum('tipo_contenido', ['pagina', 'noticia', 'entrevista', 'galeria'])->default('noticia')->change();
            
            // Crear nueva clave foránea que referencia 'galleries'
            $table->foreign('galeria_id')->references('id')->on('galleries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Revertir cambios
            $table->dropForeign(['galeria_id']);
            $table->enum('tipo_contenido', ['pagina', 'noticia', 'entrevista'])->default('noticia')->change();
            $table->foreign('galeria_id')->references('id')->on('galerias')->onDelete('set null');
        });
    }
};
