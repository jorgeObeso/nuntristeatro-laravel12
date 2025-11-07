@extends('admin.layouts.app')

@section('title', 'Editar Galería')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Editar Galería: {{ $gallery->nombre }}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.galleries.index') }}">Galerías</a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-edit"></i>
                                Información de la Galería
                            </h3>
                        </div>
                        <form action="{{ route('admin.galleries.update', $gallery) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Galería <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre', $gallery->nombre) }}" 
                                           required>
                                    @error('nombre')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="slug">Slug (URL amigable)</label>
                                    <input type="text" 
                                           class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" 
                                           name="slug" 
                                           value="{{ old('slug', $gallery->slug) }}">
                                    <small class="form-text text-muted">
                                        El slug se usa en la URL de la galería. Solo letras, números y guiones.
                                    </small>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                              id="descripcion" 
                                              name="descripcion" 
                                              rows="4"
                                              placeholder="Descripción opcional de la galería">{{ old('descripcion', $gallery->descripcion) }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="imagen_portada">Imagen de Portada</label>
                                    
                                    @if($gallery->imagen_portada)
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $gallery->imagen_portada) }}" 
                                                 alt="{{ $gallery->nombre }}"
                                                 class="img-thumbnail"
                                                 style="max-width: 200px; max-height: 150px;">
                                            <br>
                                            <small class="text-muted">Imagen actual</small>
                                        </div>
                                    @endif
                                    
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('imagen_portada') is-invalid @enderror" 
                                               id="imagen_portada" 
                                               name="imagen_portada"
                                               accept="image/*">
                                        <label class="custom-file-label" for="imagen_portada">
                                            {{ $gallery->imagen_portada ? 'Cambiar imagen...' : 'Seleccionar imagen...' }}
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        {{ $gallery->imagen_portada ? 'Deja vacío para mantener la imagen actual.' : 'Imagen opcional que representará la galería.' }}
                                    </small>
                                    @error('imagen_portada')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="orden">Orden</label>
                                    <input type="number" 
                                           class="form-control @error('orden') is-invalid @enderror" 
                                           id="orden" 
                                           name="orden" 
                                           value="{{ old('orden', $gallery->orden) }}" 
                                           min="0">
                                    <small class="form-text text-muted">
                                        Número que determina el orden de visualización. Menor número = mayor prioridad.
                                    </small>
                                    @error('orden')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Galería
                                </button>
                                <a href="{{ route('admin.galleries.show', $gallery) }}" class="btn btn-info ml-2">
                                    <i class="fas fa-images"></i> Ver Imágenes
                                </a>
                                <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-arrow-left"></i> Volver al Listado
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cog"></i>
                                Configuración
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="activa" 
                                           name="activa" 
                                           value="1" 
                                           {{ old('activa', $gallery->activa) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="activa">
                                        Galería Activa
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Las galerías inactivas no se mostrarán en el sitio web.
                                </small>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="visible_web" 
                                           name="visible_web" 
                                           value="1" 
                                           {{ old('visible_web', $gallery->visible_web) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="visible_web">
                                        Visible en Web
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Controla si la galería aparece públicamente en el sitio.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar"></i>
                                Estadísticas
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Imágenes:</strong> {{ $gallery->images()->count() }}</p>
                            <p><strong>Creada:</strong> {{ $gallery->created_at->format('d/m/Y H:i') }}</p>
                            <p><strong>Actualizada:</strong> {{ $gallery->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('scripts')
<script>
    // Actualizar label del archivo seleccionado
    document.getElementById('imagen_portada').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar imagen...';
        e.target.nextElementSibling.textContent = fileName;
    });
</script>
@endsection