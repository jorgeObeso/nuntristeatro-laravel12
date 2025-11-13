@php
    $galeria = isset($galerias) && $galerias->count() > 0 ? $galerias->first() : null;
    $imagenesGaleria = $galeria ? $galeria->images : collect();
    if (!isset($idiomaRuta)) {
        $idiomaActual = function_exists('idioma_actual') ? idioma_actual() : (app()->getLocale() ?? 'es');
        $idiomaRuta = function_exists('etiqueta_para_ruta_idioma') ? etiqueta_para_ruta_idioma($idiomaActual) : $idiomaActual;
    }
@endphp

    {{-- Galería después de las noticias --}}
    @section('galeria')
        @if($imagenesGaleria->count() > 0)
            <section class="py-5 bg-white">
                <div class="container">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
                        <div>
                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-2 mb-2">{{ __('Galería') }}</span>
                            <h3 class="fw-bold mb-0">{{ $galeria->nombre ?? ($idiomaActual === 'ast' ? 'Galería' : 'Galería') }}</h3>
                        </div>
                        <a href="{{ route('galleries.index', ['idioma' => $idiomaRuta]) }}" class="btn btn-outline-dark mt-3 mt-lg-0">
                            {{ $idiomaActual === 'ast' ? 'Ver toles les galeríes' : 'Ver todas las galerías' }}
                        </a>
                    </div>
                    <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($imagenesGaleria as $imagen)
                                <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                    <div class="d-flex justify-content-center align-items-center" style="min-height:320px;">
                                        {!! responsive_image_html(
                                            $imagen->imagen,
                                            get_gallery_image_alt($imagen, $idiomaActual),
                                            'rounded-4 shadow-lg w-100',
                                            'max-width: 600px; max-height: 320px; object-fit: cover;'
                                        ) !!}
                                    </div>
                                    @if($imagen->titulo)
                                        <div class="carousel-caption d-none d-md-block">
                                            <h5 class="fw-bold bg-dark bg-opacity-75 rounded-3 px-3 py-2 d-inline-block">{{ $imagen->titulo }}</h5>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @if($imagenesGaleria->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    @endsection
@extends('layouts.app')

@section('title', 'Inicio - ' . ($configuracion->nombre_empresa ?? 'Nuntris Teatro'))

@php
    $slidesCollection = isset($slides) ? collect($slides) : collect();
    $idiomaActual = idioma_actual();
    $idiomaRuta = etiqueta_para_ruta_idioma($idiomaActual);

    $textoInicio = null;
    if ($contenidoInicio) {
        $textoInicio = $contenidoInicio->textos->first(function ($texto) use ($idiomaActual) {
            return optional($texto->idioma)->etiqueta === $idiomaActual;
        }) ?? $contenidoInicio->textos->first();
    }
@endphp

@section('content')
    <section class="hero-section">
        @if($slidesCollection->isNotEmpty())
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="7000">
                <div class="carousel-inner">
                    @foreach($slidesCollection as $slide)
                        @php
                            $imagenUrl = $slide->imagen_url;
                            $tituloSlide = $slide->titulo ?: ($configuracion->nombre_empresa ?? 'Nuntris Teatro');
                            $descripcionSlide = $slide->descripcion ?: $configuracion->metadescripcion;
                            $ctaUrl = $slide->url;
                            $ctaTarget = $slide->nueva_ventana ? '_blank' : '_self';
                        @endphp
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="hero-slide" style="{{ $imagenUrl ? "background-image: url('{$imagenUrl}')" : 'background-color: var(--brand-primary);' }}">
                                <div class="hero-overlay">
                                    <div class="container">
                                        <div class="row justify-content-center justify-content-lg-start">
                                            <div class="col-lg-8 col-xl-6 hero-caption">
                                                <h1 class="display-5 mb-3">{{ $tituloSlide }}</h1>
                                                @if($descripcionSlide)
                                                    <p class="lead mb-4">{{ $descripcionSlide }}</p>
                                                @endif
                                                @if($ctaUrl)
                                                    <a href="{{ $ctaUrl }}" class="btn btn-warning btn-lg shadow-sm" target="{{ $ctaTarget }}">
                                                        <i class="fa-solid fa-circle-play me-2"></i>{{ __('Descubrir') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($slidesCollection->count() > 1)
                    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        @else
            <div class="hero-slide" style="background-color: var(--brand-primary);">
                <div class="hero-overlay">
                    <div class="container">
                        <div class="row justify-content-center justify-content-lg-start">
                            <div class="col-lg-8 col-xl-6 hero-caption">
                                <h1 class="display-5 mb-3">{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</h1>
                                <p class="lead mb-4">{{ $configuracion->metadescripcion ?? 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas.' }}</p>
                                <a href="{{ route('noticias', ['idioma' => $idiomaRuta]) }}" class="btn btn-warning btn-lg shadow-sm">
                                    <i class="fa-solid fa-newspaper me-2"></i>{{ __('Ver noticias') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    @if($textoInicio)
        <section class="py-5 py-lg-5 bg-white">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 col-xl-8">
                        <article class="text-center">
                            <h1 class="fw-bold mb-3">{{ $textoInicio->titulo ?? ($configuracion->nombre_empresa ?? 'Nuntris Teatro') }}</h1>
                            @if(!empty($textoInicio->subtitulo))
                                <h2 class="h4 text-secondary mb-3">{{ $textoInicio->subtitulo }}</h2>
                            @endif
                            @if(!empty($textoInicio->resumen))
                                <p class="lead mb-4">{!! $textoInicio->resumen !!}</p>
                            @endif
                            @if(!empty($textoInicio->contenido))
                                <div class="fs-5 text-start mx-auto" style="max-width: 700px;">
                                    {!! $textoInicio->contenido !!}
                                </div>
                            @endif
                        </article>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="py-5 py-lg-5">
        <div class="container">
            @if($noticiasPortada && $noticiasPortada->count() > 0)
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4">
                    <div>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-2 mb-2">{{ __('Actualidad') }}</span>
                        <h3 class="fw-bold mb-0">{{ $idiomaActual === 'ast' ? 'Últimes noticies' : 'Últimas noticias' }}</h3>
                    </div>
                    <a href="{{ route('noticias', ['idioma' => $idiomaRuta]) }}" class="btn btn-outline-dark mt-3 mt-lg-0">
                        {{ $idiomaActual === 'ast' ? 'Ver toles les noticies' : 'Ver todas las noticias' }}
                    </a>
                </div>

                <div class="row g-4">
                    @foreach($noticiasPortada as $noticia)
                        @php
                            $textoNoticia = $noticia->textos->first();
                        @endphp
                        @if($textoNoticia)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    @if($noticia->imagen_portada)
                                        {!! responsive_image_html(
                                            $noticia->imagen_portada,
                                            $textoNoticia->imagen_portada_alt ?? $noticia->imagen_portada_alt ?? $textoNoticia->titulo ?? 'Noticia',
                                            'card-img-top noticias-img'
                                        ) !!}
                                    @endif
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold">{{ $textoNoticia->titulo }}</h5>
                                        @if($noticia->fecha_publicacion)
                                            <p class="text-muted small mb-2">
                                                <i class="fa-solid fa-calendar-days me-1"></i>{{ optional($noticia->fecha_publicacion)->format('d/m/Y') }}
                                            </p>
                                        @endif
                                        @if($textoNoticia->resumen)
                                            <p class="text-muted flex-grow-1">{!! $textoNoticia->resumen !!}</p>
                                        @endif
                                        <div class="mt-3">
                                            <a href="{{ route('contenido', ['idioma' => $idiomaRuta, 'slug' => $textoNoticia->slug]) }}" class="btn btn-link px-0 fw-semibold">
                                                {{ $idiomaActual === 'ast' ? 'Lleer más' : 'Leer más' }} <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <h4 class="fw-bold mb-3">{{ $idiomaActual === 'ast' ? '¡Próximamente!' : '¡Próximamente!' }}</h4>
                        <p class="text-muted mb-0">{{ $idiomaActual === 'ast' ? 'Tamos trabayando en nuevu conteníu. Vuelvi llueu pa conocer les últimes novedaes.' : 'Estamos trabajando en nuevo contenido. Vuelve pronto para conocer las últimas novedades.' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </section>
    @yield('galeria')

    <section class="py-5 bg-white">
        <div class="container">
            <div class="card border-0 shadow-lg overflow-hidden">
                <div class="row g-0 align-items-center">
                    <div class="col-lg-7 p-5 p-lg-5">
                        <h3 class="fw-bold mb-3">{{ $idiomaActual === 'ast' ? '¿Quies saber más?' : '¿Quieres saber más?' }}</h3>
                        <p class="text-muted mb-4">{{ $idiomaActual === 'ast' ? 'Contáctanos y cuéntanos qué necesites. Tamos pa ayudar onde faga falta.' : 'Contáctanos y cuéntanos qué necesitas. Estamos para ayudarte en todo lo que haga falta.' }}</p>
                        @if($configuracion->email)
                            <a href="mailto:{{ $configuracion->email }}" class="btn btn-dark btn-lg">
                                <i class="fa-solid fa-envelope me-2"></i>{{ $idiomaActual === 'ast' ? 'Contautar' : 'Contactar' }}
                            </a>
                        @endif
                    </div>
                    <div class="col-lg-5 bg-dark position-relative text-white py-5 px-4">
                        <div class="position-absolute top-0 start-0 w-100 h-100" style="background-image: url('{{ asset('maqueta/assets/img/backgrounds/Recurso1.png') }}'); background-size: cover; background-position: center; opacity: 0.25;"></div>
                        <div class="position-relative">
                            <h4 class="fw-semibold mb-3">{{ __('Información de contacto') }}</h4>
                            <ul class="list-unstyled mb-0">
                                @if($configuracion->telefono_empresa)
                                    <li class="mb-2"><i class="fa-solid fa-phone me-2"></i>{{ $configuracion->telefono_empresa }}</li>
                                @endif
                                @if($configuracion->movil_empresa)
                                    <li class="mb-2"><i class="fa-solid fa-mobile-screen-button me-2"></i>{{ $configuracion->movil_empresa }}</li>
                                @endif
                                @if($configuracion->direccion_empresa)
                                    <li class="mb-2"><i class="fa-solid fa-location-dot me-2"></i>{{ $configuracion->direccion_empresa }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .noticias-img {
            height: 220px;
            object-fit: cover;
        }

        @media (max-width: 767px) {
            .hero-caption h1 {
                font-size: 2rem;
            }
        }
    </style>
@endpush