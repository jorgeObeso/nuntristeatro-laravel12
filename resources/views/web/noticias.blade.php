@extends('layouts.app')

@section('title', 'Noticias - ' . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-newspaper text-primary"></i> 
                    {{ app()->getLocale() == 'as' ? 'Noticies' : 'Noticias' }}
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('inicio', app()->getLocale()) }}">
                                {{ app()->getLocale() == 'as' ? 'Entamu' : 'Inicio' }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ app()->getLocale() == 'as' ? 'Noticies' : 'Noticias' }}
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    @if($noticias && $noticias->count() > 0)
        <div class="row">
            @foreach($noticias as $noticia)
                @if($noticia->textos->count() > 0)
                    @php
                        $texto = $noticia->textos->where('idioma.codigo', app()->getLocale())->first();
                    @endphp
                    @if($texto)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card news-card h-100">
                                @if($noticia->imagen_portada)
                                    <img src="{{ asset('storage/' . $noticia->imagen_portada) }}" 
                                         class="card-img-top" 
                                         alt="{{ $texto->imagen_portada_alt ?? $noticia->imagen_portada_alt ?? $texto->titulo ?? 'Noticia de ' . ($noticia->lugar ?? 'teatro') }}"
                                         style="height: 250px; object-fit: cover;">
                                @endif
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $texto->titulo }}</h5>
                                    
                                    <div class="mb-2">
                                        @if($noticia->fecha_publicacion)
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> 
                                                {{ $noticia->fecha_publicacion->format('d/m/Y') }}
                                            </small>
                                        @endif
                                        
                                        @if($noticia->lugar)
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-map-marker-alt"></i> {{ $noticia->lugar }}
                                            </small>
                                        @endif
                                    </div>
                                    
                                    @if($texto->resumen)
                                        <p class="card-text flex-grow-1">{{ $texto->resumen }}</p>
                                    @elseif($texto->contenido)
                                        <p class="card-text flex-grow-1">
                                            {{ Str::limit(strip_tags($texto->contenido), 150) }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-auto">
                                        <a href="{{ route('contenido', [app()->getLocale(), $texto->slug]) }}" 
                                           class="btn btn-primary btn-sm">
                                            {{ app()->getLocale() == 'as' ? 'Lleer más' : 'Leer más' }}
                                            <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $noticias->links() }}
            </div>
        </div>
    @else
        <!-- Mensaje cuando no hay noticias -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                        <h3>{{ app()->getLocale() == 'as' ? 'Entá nun hai noticies' : 'Aún no hay noticias' }}</h3>
                        <p class="text-muted">
                            {{ app()->getLocale() == 'as' ? 'Tamos trabayando en nuevu conteníu. Vuelvi llueu pa ver les nueses últimes noticies.' : 'Estamos trabajando en nuevo contenido. Vuelve pronto para ver nuestras últimas noticias.' }}
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
    .news-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border: none;
    }
    
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.15);
    }
    
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin: 0;
    }
</style>
@endpush