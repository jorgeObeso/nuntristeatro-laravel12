@extends('layouts.app')

@php
    $texto = $contenido->textos->where('idioma.codigo', app()->getLocale())->first();
@endphp

@section('title', $texto->metatitulo ?: $texto->titulo . ' - ' . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))
@section('meta_description', $texto->metadescripcion ?: $texto->resumen)

@section('content')
<div class="container">
    @if($texto)
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('inicio', app()->getLocale()) }}">
                        {{ app()->getLocale() == 'as' ? 'Entamu' : 'Inicio' }}
                    </a>
                </li>
                @if($contenido->tipo_contenido == 'noticia')
                    <li class="breadcrumb-item">
                        <a href="{{ route('noticias', app()->getLocale()) }}">
                            {{ app()->getLocale() == 'as' ? 'Noticies' : 'Noticias' }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $texto->titulo }}
                </li>
            </ol>
        </nav>

        <div class="row">
            <!-- Contenido principal -->
            <div class="col-lg-8">
                <article class="card">
                    @if($contenido->imagen_portada)
                        <img src="{{ asset('storage/' . $contenido->imagen_portada) }}" 
                             class="card-img-top" 
                             alt="{{ $texto->imagen_portada_alt ?? $contenido->imagen_portada_alt ?? $texto->titulo ?? 'Imagen de ' . ucfirst($contenido->tipo_contenido) }}"
                             style="max-height: 400px; object-fit: cover; width: 100%;">
                    @endif
                    
                    <div class="card-body">
                        <!-- Título -->
                        <h1 class="card-title mb-3">{{ $texto->titulo }}</h1>
                        
                        <!-- Subtítulo -->
                        @if($texto->subtitulo)
                            <h2 class="h4 text-muted mb-4">{{ $texto->subtitulo }}</h2>
                        @endif
                        
                        <!-- Metadatos -->
                        <div class="mb-4">
                            @if($contenido->fecha_publicacion && $contenido->tipo_contenido == 'noticia')
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-calendar"></i> 
                                    {{ $contenido->fecha_publicacion->format('d/m/Y') }}
                                </span>
                            @endif
                            
                            @if($contenido->lugar)
                                <span class="badge bg-secondary me-2">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    {{ $contenido->lugar }}
                                </span>
                            @endif
                            
                            <span class="badge bg-info">
                                <i class="fas fa-tag"></i> 
                                {{ ucfirst($contenido->tipo_contenido) }}
                            </span>
                        </div>
                        
                        <!-- Resumen -->
                        @if($texto->resumen)
                            <div class="alert alert-light border-start border-primary border-4 mb-4">
                                {!! $texto->resumen !!}
                            </div>
                        @endif
                        
                        <!-- Contenido -->
                        @if($texto->contenido)
                            <div class="content-body">
                                {!! $texto->contenido !!}
                            </div>
                        @endif
                    </div>
                </article>
                
                <!-- Navegación entre contenidos -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> 
                            {{ app()->getLocale() == 'as' ? 'Volver' : 'Volver' }}
                        </a>
                        
                        <div class="btn-group">
                            <a href="{{ route('cambiar-idioma', 'es') }}" 
                               class="btn {{ app()->getLocale() == 'es' ? 'btn-primary' : 'btn-outline-primary' }}">
                                ES
                            </a>
                            <a href="{{ route('cambiar-idioma', 'as') }}" 
                               class="btn {{ app()->getLocale() == 'as' ? 'btn-primary' : 'btn-outline-primary' }}">
                                AS
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Widget de información -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle"></i> 
                            {{ app()->getLocale() == 'as' ? 'Información' : 'Información' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($contenido->fecha)
                            <p class="mb-2">
                                <strong>{{ app()->getLocale() == 'as' ? 'Fecha:' : 'Fecha:' }}</strong><br>
                                {{ $contenido->fecha->format('d/m/Y') }}
                            </p>
                        @endif
                        
                        @if($contenido->lugar)
                            <p class="mb-2">
                                <strong>{{ app()->getLocale() == 'as' ? 'Llugar:' : 'Lugar:' }}</strong><br>
                                {{ $contenido->lugar }}
                            </p>
                        @endif
                        
                        @if($contenido->galeria)
                            <p class="mb-0">
                                <strong>{{ app()->getLocale() == 'as' ? 'Galería:' : 'Galería:' }}</strong><br>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-images"></i> 
                                    {{ app()->getLocale() == 'as' ? 'Ver galería' : 'Ver galería' }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
                
                <!-- Widget de contacto -->
                @if($configuracion->email || $configuracion->telefono_empresa)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-envelope"></i> 
                                {{ app()->getLocale() == 'as' ? 'Contautu' : 'Contacto' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($configuracion->email)
                                <p class="mb-2">
                                    <a href="mailto:{{ $configuracion->email }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fas fa-envelope"></i> 
                                        {{ app()->getLocale() == 'as' ? 'Unviar email' : 'Enviar email' }}
                                    </a>
                                </p>
                            @endif
                            
                            @if($configuracion->telefono_empresa)
                                <p class="mb-0">
                                    <a href="tel:{{ $configuracion->telefono_empresa }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-phone"></i> 
                                        {{ $configuracion->telefono_empresa }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Widget de redes sociales -->
                @if($configuracion->facebook || $configuracion->twitter || $configuracion->instagram || $configuracion->youtube)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-share-alt"></i> 
                                {{ app()->getLocale() == 'as' ? 'Síguenos' : 'Síguenos' }}
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            @if($configuracion->facebook)
                                <a href="{{ $configuracion->facebook }}" target="_blank" class="btn btn-primary btn-sm me-1 mb-2">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if($configuracion->twitter)
                                <a href="{{ $configuracion->twitter }}" target="_blank" class="btn btn-info btn-sm me-1 mb-2">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if($configuracion->instagram)
                                <a href="{{ $configuracion->instagram }}" target="_blank" class="btn btn-danger btn-sm me-1 mb-2">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if($configuracion->youtube)
                                <a href="{{ $configuracion->youtube }}" target="_blank" class="btn btn-dark btn-sm me-1 mb-2">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <!-- Mensaje de error cuando no se encuentra el contenido en el idioma actual -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h3>{{ app()->getLocale() == 'as' ? 'Conteníu nun disponible' : 'Contenido no disponible' }}</h3>
                        <p class="text-muted">
                            {{ app()->getLocale() == 'as' ? 'Esti conteníu nun ta disponible nel idioma actual.' : 'Este contenido no está disponible en el idioma actual.' }}
                        </p>
                        <a href="{{ route('inicio', app()->getLocale()) }}" class="btn btn-primary">
                            <i class="fas fa-home"></i> 
                            {{ app()->getLocale() == 'as' ? 'Volver al entamu' : 'Volver al inicio' }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .content-body {
        line-height: 1.8;
    }
    
    .content-body img {
        max-width: 100%;
        height: auto;
        margin: 1rem 0;
        border-radius: 0.375rem;
    }
    
    .content-body h1, 
    .content-body h2, 
    .content-body h3, 
    .content-body h4, 
    .content-body h5, 
    .content-body h6 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #333;
    }
    
    .content-body p {
        margin-bottom: 1rem;
        text-align: justify;
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin: 0;
    }
</style>
@endpush