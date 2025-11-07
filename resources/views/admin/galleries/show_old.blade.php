@extends('admin.layouts.app')

@section('title', 'Galería: ' . $gallery->nombre)

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $gallery->nombre }}</h1>
                    <p class="text-muted">{{ $gallery->descripcion }}</p>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('admin.galleries.edit', $gallery) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar Galería
                        </a>
                        <a href="{{ route('admin.galleries.index') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Panel de upload de imágenes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cloud-upload-alt"></i>
                        Subir Nuevas Imágenes
                    </h3>
                </div>
                <div class="card-body">
                    <form id="upload-form" action="{{ route('admin.galleries.upload-images', $gallery) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="images" class="form-label">Seleccionar Imágenes</label>
                            <input type="file" 
                                   class="form-control" 
                                   id="images" 
                                   name="images[]" 
                                   multiple 
                                   accept="image/*"
                                   required>
                            <small class="form-text text-muted">
                                Puedes seleccionar múltiples imágenes. Formatos: JPG, PNG, GIF, WebP. Máximo 10MB por imagen.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary" id="upload-btn">
                            <i class="fas fa-upload me-2"></i>
                            Subir Imágenes
                        </button>
                    </form>
                    
                    <div id="upload-progress" style="display: none;" class="mt-3">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 100%"></div>
                        </div>
                        <p class="mt-2">Subiendo imágenes...</p>
                    </div>
                </div>
            </div>

            <!-- Galería de imágenes -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images"></i>
                        Imágenes de la Galería ({{ $gallery->images->count() }})
                    </h3>
                    @if($gallery->images->count() > 0)
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-info" id="toggle-edit-mode">
                                <i class="fas fa-edit"></i> Modo Edición
                            </button>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($gallery->images->count() > 0)
                        <div id="images-grid" class="row sortable">
                            @foreach($gallery->images as $image)
                                <div class="col-md-3 col-sm-4 col-6 mb-4 image-item" data-id="{{ $image->id }}">
                                    <div class="card image-card">
                                        <div class="image-container position-relative">
                                            <img src="{{ asset('storage/' . $image->imagen) }}" 
                                                 alt="{{ $image->alt_text }}"
                                                 class="card-img-top"
                                                 style="height: 200px; object-fit: cover; cursor: pointer;"
                                                 onclick="openImageModal({{ $image->id }})">
                                            
                                            <!-- Overlay con controles -->
                                            <div class="image-overlay">
                                                <div class="overlay-controls">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-primary" 
                                                            onclick="editImage({{ $image->id }})"
                                                            title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="deleteImage({{ $image->id }})"
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="drag-handle">
                                                    <i class="fas fa-arrows-alt"></i>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body p-2">
                                            <h6 class="card-title mb-1">
                                                {{ $image->titulo ?: 'Sin título' }}
                                            </h6>
                                            <small class="text-muted">
                                                Orden: {{ $image->orden }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h4>No hay imágenes en esta galería</h4>
                            <p class="text-muted">Sube algunas imágenes para comenzar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para vista completa de imagen -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista de Imagen</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modal-image" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
    .upload-area {
        border: 2px dashed #007bff;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .upload-area.drag-over {
        border-color: #28a745;
        background-color: #d4edda;
    }
    
    .image-card {
        transition: transform 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    
    .image-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .image-container {
        position: relative;
        overflow: hidden;
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .image-card:hover .image-overlay {
        opacity: 1;
    }
    
    .overlay-controls {
        display: flex;
        gap: 10px;
    }
    
    .drag-handle {
        position: absolute;
        top: 10px;
        right: 10px;
        color: white;
        cursor: move;
    }
    
    .sortable .image-item {
        cursor: move;
    }
    
    .sortable .image-item.dragging {
        opacity: 0.5;
    }
    
    .edit-mode .image-overlay {
        opacity: 1;
        background: rgba(0,0,0,0.5);
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const galleryId = {{ $gallery->id }};
    let editMode = false;
    
    // Configurar drag & drop para upload
    const uploadArea = document.getElementById('upload-area');
    const imageInput = document.getElementById('image-input');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });
    
    function highlight(e) {
        uploadArea.classList.add('drag-over');
    }
    
    function unhighlight(e) {
        uploadArea.classList.remove('drag-over');
    }
    
    uploadArea.addEventListener('drop', handleDrop, false);
    imageInput.addEventListener('change', handleFiles, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        handleFiles({ target: { files: files } });
    }
    
    function handleFiles(e) {
        const files = Array.from(e.target.files);
        if (files.length > 0) {
            uploadImages(files);
        }
    }
    
    // Función para subir imágenes
    function uploadImages(files) {
        const formData = new FormData();
        files.forEach(file => {
            formData.append('images[]', file);
        });
        formData.append('_token', '{{ csrf_token() }}');
        
        // Mostrar progreso
        document.getElementById('upload-content').style.display = 'none';
        document.getElementById('upload-progress').style.display = 'block';
        
        fetch(`/admin/galleries/${galleryId}/upload-images`, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Upload response:', data);
            
            if (data.success) {
                alert(`${data.message}`);
                // Recargar la página para mostrar las nuevas imágenes
                location.reload();
            } else {
                let errorMsg = 'Error al subir las imágenes';
                if (data.message) {
                    errorMsg += ': ' + data.message;
                }
                if (data.errors && data.errors.length > 0) {
                    errorMsg += '\n\nDetalles:\n' + data.errors.join('\n');
                }
                alert(errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión al subir las imágenes: ' + error.message);
        })
        .finally(() => {
            // Restaurar estado del upload
            document.getElementById('upload-content').style.display = 'block';
            document.getElementById('upload-progress').style.display = 'none';
            imageInput.value = '';
        });
    }
    
    // Configurar sortable para reordenar imágenes
    const imagesGrid = document.getElementById('images-grid');
    if (imagesGrid) {
        const sortable = Sortable.create(imagesGrid, {
            disabled: true,
            animation: 150,
            ghostClass: 'dragging',
            onEnd: function(evt) {
                updateImageOrder();
            }
        });
        
        // Toggle modo edición
        document.getElementById('toggle-edit-mode')?.addEventListener('click', function() {
            editMode = !editMode;
            sortable.option('disabled', !editMode);
            
            if (editMode) {
                document.body.classList.add('edit-mode');
                this.innerHTML = '<i class="fas fa-save"></i> Guardar Orden';
                this.classList.remove('btn-info');
                this.classList.add('btn-success');
            } else {
                document.body.classList.remove('edit-mode');
                this.innerHTML = '<i class="fas fa-edit"></i> Modo Edición';
                this.classList.remove('btn-success');
                this.classList.add('btn-info');
            }
        });
    }
    
    // Actualizar orden de imágenes
    function updateImageOrder() {
        const items = Array.from(document.querySelectorAll('.image-item'));
        const imageOrder = items.map((item, index) => ({
            id: item.dataset.id,
            orden: index
        }));
        
        fetch(`/admin/galleries/${galleryId}/image-order`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ images: imageOrder })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Orden actualizado');
            }
        });
    }
    
    // Funciones para gestión de imágenes
    function openImageModal(imageId) {
        // Implementar vista de imagen completa
        console.log('Abrir imagen:', imageId);
    }
    
    function editImage(imageId) {
        // Implementar edición de imagen
        console.log('Editar imagen:', imageId);
    }
    
    function deleteImage(imageId) {
        if (confirm('¿Estás seguro de eliminar esta imagen?')) {
            // Implementar eliminación
            console.log('Eliminar imagen:', imageId);
        }
    }
</script>
@endsection