@extends('admin.layouts.app')

@section('title', 'Gestión de Contenidos')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Contenidos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Contenidos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Filtros y acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.contents.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Contenido
                </a>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <select name="tipo" class="form-control mr-2" onchange="this.form.submit()">
                        <option value="">Todos los tipos</option>
                        <option value="pagina" {{ request('tipo') == 'pagina' ? 'selected' : '' }}>Páginas</option>
                        <option value="noticia" {{ request('tipo') == 'noticia' ? 'selected' : '' }}>Noticias</option>
                        <option value="entrevista" {{ request('tipo') == 'entrevista' ? 'selected' : '' }}>Entrevistas</option>
                        <option value="galeria" {{ request('tipo') == 'galeria' ? 'selected' : '' }}>Galerías</option>
                    </select>
                    <input type="text" name="search" class="form-control mr-2" placeholder="Buscar por título..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <!-- Tabla de contenidos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Contenidos</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título (ES)</th>
                            <th>Slug/URL</th>
                            <th>Tipo</th>
                            <th>Fecha Publicación</th>
                            <th>Estado</th>
                            <th>Imágenes</th>
                            <th>Portada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contents as $content)
                            @php
                                $idiomasActivos = \App\Models\Idioma::where('activo', true)->orderBy('orden')->get();
                                $textoPrincipal = null;
                                $todosLosTextos = collect();
                                
                                foreach($idiomasActivos as $idioma) {
                                    $texto = $content->textos->where('idioma_id', $idioma->id)->first();
                                    if ($texto) {
                                        $todosLosTextos->push($texto);
                                        if ($idioma->es_principal || !$textoPrincipal) {
                                            $textoPrincipal = $texto;
                                        }
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $content->id }}</td>
                                <td>
                                    <strong>{{ $textoPrincipal ? $textoPrincipal->titulo : 'Sin título' }}</strong>
                                    @if($content->lugar)
                                        <br><small class="text-muted">📍 {{ $content->lugar }}</small>
                                    @endif
                                    
                                    {{-- Mostrar idiomas disponibles --}}
                                    <br>
                                    <div class="mt-1">
                                        @foreach($idiomasActivos as $idioma)
                                            @php
                                                $textoIdioma = $content->textos->where('idioma_id', $idioma->id)->first();
                                            @endphp
                                            @if($textoIdioma && $textoIdioma->titulo)
                                                <span class="badge bg-success me-1" title="{{ $textoIdioma->titulo }}">
                                                    {{ strtoupper($idioma->etiqueta) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary me-1" title="Sin contenido en {{ $idioma->nombre }}">
                                                    {{ strtoupper($idioma->etiqueta) }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    @if($todosLosTextos->isNotEmpty())
                                        @foreach($todosLosTextos as $texto)
                                            @if($texto->slug)
                                                <code>/{{ $texto->idioma->etiqueta }}/{{ $texto->slug }}</code>
                                                @if(!$loop->last)<br>@endif
                                            @endif
                                        @endforeach
                                        @if($todosLosTextos->where('slug', null)->isNotEmpty())
                                            <br><small class="text-warning">Algunos idiomas sin slug</small>
                                        @endif
                                    @else
                                        <span class="text-muted">Sin URLs</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ 
                                        $content->tipo_contenido == 'noticia' ? 'info' : 
                                        ($content->tipo_contenido == 'pagina' ? 'success' : 
                                        ($content->tipo_contenido == 'galeria' ? 'secondary' : 'warning'))
                                    }}">
                                        {{ ucfirst($content->tipo_contenido) }}
                                    </span>
                                </td>
                                <td>
                                    @if($content->fecha_publicacion)
                                        {{ \Carbon\Carbon::parse($content->fecha_publicacion)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Sin fecha</span>
                                    @endif
                                </td>
                                <td>
                                    @if($textoPrincipal && $textoPrincipal->visible)
                                        <span class="badge badge-success">Visible</span>
                                    @else
                                        <span class="badge badge-secondary">Oculto</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex">
                                        @if($content->imagen)
                                            <img src="{{ asset('storage/' . $content->imagen) }}" 
                                                 alt="Imagen" 
                                                 class="img-thumbnail me-1" 
                                                 style="width: 30px; height: 30px; object-fit: cover;"
                                                 title="Imagen principal">
                                        @endif
                                        @if($content->imagen_portada)
                                            <img src="{{ asset('storage/' . $content->imagen_portada) }}" 
                                                 alt="Portada" 
                                                 class="img-thumbnail" 
                                                 style="width: 30px; height: 30px; object-fit: cover;"
                                                 title="Imagen de portada">
                                        @endif
                                        @if(!$content->imagen && !$content->imagen_portada)
                                            <span class="text-muted"><i class="fas fa-image"></i> Sin imágenes</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($content->portada)
                                        <i class="fas fa-star text-warning" title="En portada"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.contents.show', $content) }}" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.contents.edit', $content) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.contents.destroy', $content) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este contenido?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0">No se encontraron contenidos</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($contents->hasPages())
                <div class="card-footer">
                    {{ $contents->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td {
            vertical-align: middle;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
    </style>
@stop

@section('js')
    <script>
        // Auto-submit del formulario de filtros cuando cambia el select
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif
        });
    </script>
@stop