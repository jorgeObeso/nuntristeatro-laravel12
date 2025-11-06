<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ImageConfig;

class ImageConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            // Configuraciones para NOTICIAS
            [
                'tipo_contenido' => 'noticia',
                'tipo_imagen' => 'imagen',
                'ancho' => 800,
                'alto' => 600,
                'mantener_aspecto' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'redimensionar' => true,
                'activo' => true,
            ],
            [
                'tipo_contenido' => 'noticia',
                'tipo_imagen' => 'imagen_portada',
                'ancho' => 400,
                'alto' => 300,
                'mantener_aspecto' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'redimensionar' => true,
                'activo' => true,
            ],

            // Configuraciones para PÁGINAS
            [
                'tipo_contenido' => 'pagina',
                'tipo_imagen' => 'imagen',
                'ancho' => 1200,
                'alto' => 800,
                'mantener_aspecto' => true,
                'formato' => 'jpg',
                'calidad' => 90,
                'redimensionar' => true,
                'activo' => true,
            ],
            [
                'tipo_contenido' => 'pagina',
                'tipo_imagen' => 'imagen_portada',
                'ancho' => 600,
                'alto' => 400,
                'mantener_aspecto' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'redimensionar' => true,
                'activo' => true,
            ],

            // Configuraciones para ENTREVISTAS
            [
                'tipo_contenido' => 'entrevista',
                'tipo_imagen' => 'imagen',
                'ancho' => 800,
                'alto' => 600,
                'mantener_aspecto' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'redimensionar' => true,
                'activo' => true,
            ],
            [
                'tipo_contenido' => 'entrevista',
                'tipo_imagen' => 'imagen_portada',
                'ancho' => 400,
                'alto' => 300,
                'mantener_aspecto' => true,
                'formato' => 'jpg',
                'calidad' => 85,
                'redimensionar' => true,
                'activo' => true,
            ],
        ];

        foreach ($configs as $config) {
            ImageConfig::updateOrCreate(
                [
                    'tipo_contenido' => $config['tipo_contenido'],
                    'tipo_imagen' => $config['tipo_imagen']
                ],
                $config
            );
        }
    }
}
