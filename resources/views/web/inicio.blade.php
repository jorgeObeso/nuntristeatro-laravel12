@extends('layouts.app')

@section('title', 'Inicio - ' . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))

@section('content')
<div class="container">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="jumbotron bg-primary text-white p-5 rounded">
                <div class="container">
                    <h1 class="display-4">{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</h1>
                    <p class="lead">{{ $configuracion->metadescripcion ?? 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas' }}</p>
                    <hr class="my-4">
                    <p>Descubre nuestras obras, noticias y actividades</p>
                    <a class="btn btn-light btn-lg" href="{{ route('noticias', app()->getLocale()) }}" role="button">
                        <i class="fas fa-newspaper"></i> Ver Noticias
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido de Inicio -->
    @if($contenidoInicio && $contenidoInicio->textos->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @foreach($contenidoInicio->textos as $texto)
                            @if($texto->idioma->codigo == app()->getLocale())
                                <h2 class="card-title">{{ $texto->titulo }}</h2>
                                @if($texto->subtitulo)
                                    <h5 class="card-subtitle mb-3 text-muted">{{ $texto->subtitulo }}</h5>
                                @endif
                                @if($texto->contenido)
                                    <div class="card-text">
                                        {!! $texto->contenido !!}
                                    </div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Noticias Destacadas -->
    @if($noticiasPortada && $noticiasPortada->count() > 0)
        <div class="row">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-newspaper text-primary"></i> 
                    {{ app()->getLocale() == 'as' ? 'Últimes Noticies' : 'Últimas Noticias' }}
                </h3>
            </div>
        </div>
        
        <div class="row">
            @foreach($noticiasPortada as $noticia)
                @if($noticia->textos->count() > 0)
                    @php
                        $texto = $noticia->textos->where('idioma.codigo', app()->getLocale())->first();
                    @endphp
                    @if($texto)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card news-card h-100">
                                @if($noticia->imagen_portada)
                                    {!! responsive_image_html(
                                        $noticia->imagen_portada, 
                                        $texto->imagen_portada_alt ?? $noticia->imagen_portada_alt ?? $texto->titulo ?? 'Noticia de ' . ($noticia->lugar ?? 'teatro'),
                                        'card-img-top',
                                        'height: 200px; object-fit: cover;'
                                    ) !!}
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $texto->titulo }}</h5>
                                    
                                    @if($noticia->fecha_publicacion)
                                        <small class="text-muted mb-2">
                                            <i class="fas fa-calendar"></i> 
                                            {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                        </small>
                                    @endif
                                    
                                    @if($texto->resumen)
                                        <p class="card-text flex-grow-1">{!! $texto->resumen !!}</p>
                                    @endif
                                    
                                    @if($noticia->lugar)
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt"></i> {{ $noticia->lugar }}
                                        </small>
                                    @endif
                                    
                                    <div class="mt-auto pt-3">
                                        <a href="{{ route('contenido', [app()->getLocale(), $texto->slug]) }}" 
                                           class="btn btn-primary btn-sm">
                                            {{ app()->getLocale() == 'as' ? 'Lleer más' : 'Leer más' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center">
                <a href="{{ route('noticias', app()->getLocale()) }}" class="btn btn-outline-primary">
                    {{ app()->getLocale() == 'as' ? 'Ver toles les noticies' : 'Ver todas las noticias' }}
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    @else
        <!-- Mensaje cuando no hay noticias -->
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <h4>{{ app()->getLocale() == 'as' ? '¡Próximamente!' : '¡Próximamente!' }}</h4>
                    <p>{{ app()->getLocale() == 'as' ? 'Tamos trabayando en nuevu conteníu. Vuelvi llueu pa ver les nueses últimes noticies y actividaes.' : 'Estamos trabajando en nuevo contenido. Vuelve pronto para ver nuestras últimas noticias y actividades.' }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Sección de Contacto Rápido -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h4 class="card-title">
                        {{ app()->getLocale() == 'as' ? '¿Quies saber más?' : '¿Quieres saber más?' }}
                    </h4>
                    <p class="card-text">
                        {{ app()->getLocale() == 'as' ? 'Contáutanos pa más información sobre les nueses obres y actividaes.' : 'Contáctanos para más información sobre nuestras obras y actividades.' }}
                    </p>
                    @if($configuracion->email)
                        <a href="mailto:{{ $configuracion->email }}" class="btn btn-primary">
                            <i class="fas fa-envelope"></i> 
                            {{ app()->getLocale() == 'as' ? 'Contautar' : 'Contactar' }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .jumbotron {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .news-card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
    }
    
    .news-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
</style>
@endpush