<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear tabla temporal con nueva estructura
        Schema::create('idiomas_new', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->comment('Nombre completo del idioma (ej: Español, English, Asturiano)');
            $table->string('etiqueta', 10)->unique()->comment('Código ISO del idioma (ej: es, en, ast) para HTML lang');
            $table->string('imagen', 255)->nullable()->comment('Ruta a la imagen/bandera del idioma');
            $table->boolean('activo')->default(true)->comment('Si el idioma está disponible en el sitio web');
            $table->boolean('es_principal')->default(false)->comment('Si es el idioma por defecto del sitio');
            $table->integer('orden')->default(0)->comment('Orden de visualización en selectores');
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['activo']);
            $table->index(['es_principal']);
            $table->index(['orden']);
        });

        // Migrar datos existentes
        DB::statement('
            INSERT INTO idiomas_new (id, nombre, etiqueta, imagen, activo, es_principal, orden, created_at, updated_at)
            SELECT 
                id,
                COALESCE(idioma, "Idioma " || id) as nombre,
                COALESCE(codigo, "lang" || id) as etiqueta,
                imagen,
                COALESCE(activado, 1) as activo,
                COALESCE(principal, 0) as es_principal,
                id as orden,
                COALESCE(created_at, datetime("now")) as created_at,
                COALESCE(updated_at, datetime("now")) as updated_at
            FROM idiomas
        ');

        // Eliminar tabla antigua y renombrar la nueva
        Schema::drop('idiomas');
        Schema::rename('idiomas_new', 'idiomas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Crear tabla con estructura original
        Schema::create('idiomas_old', function (Blueprint $table) {
            $table->id();
            $table->string('idioma');
            $table->string('codigo');
            $table->string('label');
            $table->string('imagen')->nullable();
            $table->boolean('principal')->default(false);
            $table->boolean('activado')->default(true);
            $table->timestamps();
        });

        // Migrar datos de vuelta
        DB::statement('
            INSERT INTO idiomas_old (id, idioma, codigo, label, imagen, principal, activado, created_at, updated_at)
            SELECT 
                id,
                nombre as idioma,
                etiqueta as codigo,
                etiqueta as label,
                imagen,
                es_principal as principal,
                activo as activado,
                created_at,
                updated_at
            FROM idiomas
        ');

        // Eliminar tabla nueva y restaurar la original
        Schema::drop('idiomas');
        Schema::rename('idiomas_old', 'idiomas');
    }
};
