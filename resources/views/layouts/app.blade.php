<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', $configuracion->metatitulo ?? 'Nuntris Teatro')</title>
    <meta name="description" content="@yield('meta_description', $configuracion->metadescripcion ?? 'Compañía de teatro asturiana')">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Styles -->
    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 2rem 0;
        }
        .language-switcher {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
        .content-section {
            min-height: 60vh;
            padding: 2rem 0;
        }
        .news-card {
            transition: transform 0.3s ease;
        }
        .news-card:hover {
            transform: translateY(-5px);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Language Switcher -->
    <div class="language-switcher">
        <div class="btn-group">
            <a href="{{ route('cambiar-idioma', 'es') }}" class="btn btn-sm {{ app()->getLocale() == 'es' ? 'btn-primary' : 'btn-outline-primary' }}">
                ES
            </a>
            <a href="{{ route('cambiar-idioma', 'as') }}" class="btn btn-sm {{ app()->getLocale() == 'as' ? 'btn-primary' : 'btn-outline-primary' }}">
                AS
            </a>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('inicio', app()->getLocale()) }}">
                {{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('inicio', app()->getLocale()) }}">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    @isset($menus)
                        @foreach($menus as $menu)
                            <li class="nav-item dropdown">
                                <a class="nav-link {{ $menu->children->count() > 0 ? 'dropdown-toggle' : '' }}" 
                                   href="{{ $menu->url ?: ($menu->content ? route('contenido', [app()->getLocale(), $menu->content->textos->first()->slug ?? 'contenido']) : '#') }}"
                                   {{ $menu->children->count() > 0 ? 'data-bs-toggle=dropdown' : '' }}>
                                    @if($menu->icon)
                                        <i class="{{ $menu->icon }}"></i>
                                    @endif
                                    {{ $menu->title }}
                                </a>
                                @if($menu->children->count() > 0)
                                    <ul class="dropdown-menu">
                                        @foreach($menu->children as $submenu)
                                            <li>
                                                <a class="dropdown-item" 
                                                   href="{{ $submenu->url ?: ($submenu->content ? route('contenido', [app()->getLocale(), $submenu->content->textos->first()->slug ?? 'contenido']) : '#') }}">
                                                    {{ $submenu->title }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    @endisset
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('noticias', app()->getLocale()) }}">
                            <i class="fas fa-newspaper"></i> Noticias
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="content-section">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}</h5>
                    <p>{{ $configuracion->metadescripcion ?? 'Compañía de teatro asturiana especializada en obras clásicas y contemporáneas' }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Contacto</h6>
                    @if($configuracion->email ?? false)
                        <p><i class="fas fa-envelope"></i> {{ $configuracion->email }}</p>
                    @endif
                    @if($configuracion->telefono_empresa ?? false)
                        <p><i class="fas fa-phone"></i> {{ $configuracion->telefono_empresa }}</p>
                    @endif
                    
                    <!-- Social Media Links -->
                    <div class="mt-3">
                        @if($configuracion->facebook ?? false)
                            <a href="{{ $configuracion->facebook }}" class="text-white me-3" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        @endif
                        @if($configuracion->twitter ?? false)
                            <a href="{{ $configuracion->twitter }}" class="text-white me-3" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                        @endif
                        @if($configuracion->instagram ?? false)
                            <a href="{{ $configuracion->instagram }}" class="text-white me-3" target="_blank">
                                <i class="fab fa-instagram"></i>
                            </a>
                        @endif
                        @if($configuracion->youtube ?? false)
                            <a href="{{ $configuracion->youtube }}" class="text-white me-3" target="_blank">
                                <i class="fab fa-youtube"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <hr class="mt-4">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ $configuracion->nombre_empresa ?? 'Nuntris Teatro' }}. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>