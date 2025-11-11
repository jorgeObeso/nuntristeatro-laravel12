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
        Schema::table('textos_idiomas', function (Blueprint $table) {
            // Agregar campos polimórficos para soportar diferentes modelos
            $table->string('objeto_type')->nullable()->after('tipo_contenido_id');
            $table->unsignedBigInteger('objeto_id')->nullable()->after('objeto_type');
            $table->string('campo')->nullable()->after('objeto_id'); // Para identificar qué campo es (titulo, descripcion, etc.)
            $table->text('texto')->nullable()->after('campo'); // Campo genérico para el texto
            $table->boolean('activo')->default(true)->after('visible');
            
            // Hacer contenido_id nullable ya que ahora tenemos referencias polimórficas
            $table->foreignId('contenido_id')->nullable()->change();
            
            // Agregar índices para mejor rendimiento
            $table->index(['objeto_type', 'objeto_id']);
            $table->index(['objeto_type', 'objeto_id', 'idioma_id', 'campo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('textos_idiomas', function (Blueprint $table) {
            $table->dropIndex(['objeto_type', 'objeto_id']);
            $table->dropIndex(['objeto_type', 'objeto_id', 'idioma_id', 'campo']);
            $table->dropColumn(['objeto_type', 'objeto_id', 'campo', 'texto', 'activo']);
            $table->foreignId('contenido_id')->nullable(false)->change();
        });
    }
};
