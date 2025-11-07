@extends('admin.layouts.app')

@section('title', 'Crear Galería')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Crear Nueva Galería</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.galleries.index') }}">Galerías</a></li>
                        <li class="breadcrumb-item active">Crear</li>
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
                                <i class="fas fa-plus"></i>
                                Información de la Galería
                            </h3>
                        </div>
                        <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="nombre">Nombre de la Galería <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('nombre') is-invalid @enderror" 
                                           id="nombre" 
                                           name="nombre" 
                                           value="{{ old('nombre') }}" 
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
                                           value="{{ old('slug') }}"
                                           placeholder="Se generará automáticamente si se deja vacío">
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
                                              placeholder="Descripción opcional de la galería">{{ old('descripcion') }}</textarea>
                                    @error('descripcion')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="imagen_portada">Imagen de Portada</label>
                                    <div class="custom-file">
                                        <input type="file" 
                                               class="custom-file-input @error('imagen_portada') is-invalid @enderror" 
                                               id="imagen_portada" 
                                               name="imagen_portada"
                                               accept="image/*">
                                        <label class="custom-file-label" for="imagen_portada">Seleccionar imagen...</label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Imagen opcional que representará la galería. Se redimensionará automáticamente.
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
                                           value="{{ old('orden', 0) }}" 
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
                                    <i class="fas fa-save"></i> Crear Galería
                                </button>
                                <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times"></i> Cancelar
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
                                           {{ old('activa', true) ? 'checked' : '' }}>
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
                                           {{ old('visible_web', true) ? 'checked' : '' }}>
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
                                <i class="fas fa-info-circle"></i>
                                Información
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Sistema Responsive:</strong> Las imágenes se procesarán automáticamente para generar versiones optimizadas para desktop y móvil.</p>
                            <p><strong>Formatos soportados:</strong> JPG, PNG, GIF, WebP</p>
                            <p><strong>Tamaño máximo:</strong> 5MB por imagen</p>
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
    // Auto-generar slug a partir del nombre
    document.getElementById('nombre').addEventListener('input', function(e) {
        const slugField = document.getElementById('slug');
        if (!slugField.value || slugField.dataset.autoGenerated !== 'false') {
            slugField.value = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim('-');
            slugField.dataset.autoGenerated = 'true';
        }
    });

    // Marcar slug como editado manualmente
    document.getElementById('slug').addEventListener('input', function(e) {
        e.target.dataset.autoGenerated = 'false';
    });

    // Actualizar label del archivo seleccionado
    document.getElementById('imagen_portada').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Seleccionar imagen...';
        e.target.nextElementSibling.textContent = fileName;
    });
</script>
@endsection