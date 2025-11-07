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
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-info" id="toggle-sort-btn">
                            <i class="fas fa-sort" id="sort-icon"></i>
                            <span id="sort-text">Modo Ordenar</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($gallery->images->count() > 0)
                        <div id="sort-instructions" class="alert alert-info" style="display: none;">
                            <i class="fas fa-info-circle"></i>
                            <strong>Modo Ordenar Activo:</strong> Arrastra las imágenes para cambiar su orden, o edita los números directamente. Los cambios se guardan automáticamente.
                        </div>
                        
                        <div id="images-grid" class="sortable-container">
                            @foreach($gallery->images->sortBy('orden') as $image)
                                <div class="image-item" 
                                     data-id="{{ $image->id }}" 
                                     data-orden="{{ $image->orden }}"
                                     draggable="false">
                                    <div class="card image-card">
                                        <!-- Indicador de orden siempre visible -->
                                        <div class="orden-badge">
                                            <span class="orden-number">{{ $image->orden }}</span>
                                        </div>
                                        
                                        <!-- Campo de orden editable -->
                                        <div class="orden-input">
                                            <input type="number" 
                                                   class="form-control form-control-sm orden-field" 
                                                   value="{{ $image->orden }}" 
                                                   min="1" 
                                                   data-image-id="{{ $image->id }}">
                                        </div>
                                        
                                        <div class="image-container position-relative">
                                            <img src="{{ asset('storage/' . $image->imagen) }}" 
                                                 alt="{{ $image->alt_text }}"
                                                 class="card-img-top"
                                                 style="height: 200px; object-fit: cover; width: 100%;">
                                            
                                            <!-- Overlay con controles -->
                                            <div class="image-overlay">
                                                <div class="overlay-controls">
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="deleteImage({{ $image->id }})"
                                                            title="Eliminar imagen">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body">
                                            <div class="orden-display">
                                                Orden: {{ $image->orden }}
                                                @if($loop->first)
                                                    <span class="badge bg-success ms-2">Portada</span>
                                                @endif
                                            </div>
                                            @if($image->alt_text)
                                                <p class="card-text">
                                                    <small class="text-muted">Alt: {{ $image->alt_text }}</small>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <h4>No hay imágenes en esta galería</h4>
                            <p class="text-muted">Sube algunas imágenes usando el formulario de arriba.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('styles')
<style>
    /* Contenedor principal de imágenes */
    .sortable-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        padding: 20px 0;
    }

    .image-item {
        position: relative;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .image-card {
        position: relative;
        overflow: hidden;
        border: 2px solid transparent;
        transition: all 0.2s ease;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .image-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
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
    
    /* Indicador de orden siempre visible */
    .orden-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    /* Campo de orden editable */
    .orden-input {
        position: absolute;
        top: 10px;
        right: 10px;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 15;
        width: 60px;
    }
    
    .orden-field {
        text-align: center;
        font-size: 12px;
        height: 30px;
    }
    
    /* Estados del drag & drop nativo */
    .image-item[draggable="true"] {
        cursor: grab;
        transition: all 0.2s ease;
        user-select: none;
    }
    
    .image-item[draggable="true"]:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .image-item[draggable="true"]:active {
        cursor: grabbing;
    }
    
    .image-item[style*="opacity: 0.5"] {
        opacity: 0.5 !important;
        transform: rotate(5deg) scale(0.9);
        z-index: 1000;
        box-shadow: 0 10px 25px rgba(0,0,0,0.4);
    }
    
    .image-item.drag-over {
        border: 3px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1);
        transform: scale(1.05);
    }
    
    /* Modo ordenar activo */
    .sort-mode .image-item {
        border: 2px dashed #007bff;
        margin: 5px;
    }
    
    .sort-mode .image-card {
        border-color: #007bff;
        background: #f8f9fa;
        cursor: grab;
    }
    
    .sort-mode .image-item[draggable="true"] {
        cursor: grab;
    }
    
    .sort-mode .orden-badge {
        background: #28a745;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
    }
    
    .sort-mode .orden-input {
        opacity: 1;
    }
    
    .sort-mode .orden-field {
        border-color: #007bff;
        background: white;
    }
    
    .sort-mode .image-overlay {
        display: none !important;
    }
    
    /* Campo de orden */
    .orden-input {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 20;
        width: 50px;
        opacity: 0.3;
        transition: opacity 0.2s ease;
    }
    
    .orden-input:hover {
        opacity: 1;
    }
    
    .orden-field {
        text-align: center;
        font-weight: bold;
        background: rgba(255,255,255,0.95);
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
    }
    
    /* Estados del drag & drop nativo */
    .image-item[draggable="true"] {
        cursor: move;
        transition: all 0.2s ease;
    }
    
    .image-item[style*="opacity: 0.5"] {
        opacity: 0.5 !important;
        transform: rotate(2deg) scale(0.95);
        z-index: 1000;
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .image-item.drag-over {
        border: 3px solid #28a745 !important;
        background-color: rgba(40, 167, 69, 0.1);
        transform: scale(1.02);
    }
    
    /* Modo ordenar activo */
    .sort-mode .image-card {
        border-color: #007bff;
        background: #f8f9fa;
        cursor: move;
    }
    
    .sort-mode .image-item {
        border: 2px dashed #007bff;
    }
    
    .sort-mode .image-item:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }
    
    .sort-mode .drag-handle {
        opacity: 1;
        background: #007bff;
        color: white;
    }
    
    .sort-mode .orden-input {
        opacity: 1;
    }
    
    .sort-mode .orden-field {
        border-color: #007bff;
        background: white;
    }
    
    .sort-mode .image-overlay {
        display: none !important;
    }
    
    /* Animación de actualización */
    .updating-order {
        border-color: #28a745 !important;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sortable-container {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
    }
</style>
@endsection

@push('scripts')
<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
// ¡ALERTA INMEDIATA PARA VERIFICAR QUE FUNCIONA!
alert('🔥 JavaScript con Drag & Drop cargado correctamente!');

console.log('🔥 SISTEMA CON DRAG & DROP INICIADO');

// Variables globales
let sortMode = false;
let sortableInstance = null;

// Función principal para probar la conexión
function testConnection() {
    console.log('🧪 Probando conexión al servidor...');
    
    fetch('/admin/galleries/{{ $gallery->id }}/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            updates: [
                { id: 1, orden: 1 }
            ]
        })
    })
    .then(response => {
        console.log('📡 Status:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('📦 Respuesta:', text);
        alert('Conexión OK: ' + text);
    })
    .catch(error => {
        console.error('❌ Error:', error);
        alert('Error: ' + error);
    });
}

// Función simple para cambiar orden
function cambiarOrden(imageId, nuevoOrden) {
    console.log(`� Cambiando orden: Imagen ${imageId} → ${nuevoOrden}`);
    
    if (!nuevoOrden || nuevoOrden < 1) {
        alert('❌ El orden debe ser mayor a 0');
        return;
    }
    
    // Mostrar que está procesando
    const input = document.querySelector(`input[data-image-id="${imageId}"]`);
    if (input) {
        input.style.backgroundColor = '#fff3cd';
        input.disabled = true;
    }
    
    // Enviar al servidor
    fetch('/admin/galleries/{{ $gallery->id }}/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            updates: [
                { 
                    id: parseInt(imageId), 
                    orden: parseInt(nuevoOrden) 
                }
            ]
        })
    })
    .then(response => {
        console.log('📡 Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('📦 Datos recibidos:', data);
        
        if (data.success) {
            alert('✅ ¡Orden actualizado exitosamente!');
            // Recargar página para ver cambios
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            alert('❌ Error del servidor: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('❌ Error completo:', error);
        alert('❌ Error de conexión: ' + error.message);
    })
    .finally(() => {
        // Restaurar input
        if (input) {
            input.style.backgroundColor = '';
            input.disabled = false;
        }
    });
}

// Configurar eventos cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    alert('📋 Configurando eventos...');
    console.log('📋 Configurando eventos...');
    
    // Configurar botón de modo ordenar
    const sortButton = document.getElementById('toggle-sort-btn');
    if (sortButton) {
        sortButton.addEventListener('click', toggleSortMode);
        console.log('🔘 Botón de ordenar configurado');
        alert('🔘 Botón encontrado y configurado');
    } else {
        console.error('❌ No se encontró el botón de ordenar');
        alert('❌ ERROR: No se encontró el botón de ordenar');
    }
    
    // Encontrar todos los campos de orden
    const orderFields = document.querySelectorAll('.orden-field');
    console.log(`🔍 Encontrados ${orderFields.length} campos de orden`);
    alert(`🔍 Encontrados ${orderFields.length} campos de orden`);
    
    orderFields.forEach((field, index) => {
        console.log(`🎯 Configurando campo ${index + 1}:`, field);
        
        // Evento cuando cambia el valor
        field.addEventListener('change', function() {
            const imageId = this.getAttribute('data-image-id');
            const newOrder = this.value;
            console.log(`🔄 Campo cambió: Imagen ${imageId}, nuevo orden: ${newOrder}`);
            cambiarOrden(imageId, newOrder);
        });
        
        // Evento cuando presiona Enter
        field.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                console.log('⏎ Enter presionado');
                e.preventDefault();
                this.blur(); // Esto dispara el evento change
            }
        });
        
        // Evento de clic en las flechas del input number
        field.addEventListener('input', function() {
            console.log('🔢 Input cambió:', this.value);
            // Opcional: cambiar automáticamente sin esperar a blur
        });
    });
    
    console.log('✅ Eventos configurados correctamente');
});

// Función para toggle del modo ordenar (simplificado)
function toggleSortMode() {
    sortMode = !sortMode;
    console.log('� Modo ordenar:', sortMode ? 'ACTIVADO' : 'DESACTIVADO');
    
    const container = document.querySelector('.sortable-container');
    const instructions = document.getElementById('sort-instructions');
    const sortIcon = document.getElementById('sort-icon');
    const sortText = document.getElementById('sort-text');
    
    if (sortMode) {
        container.classList.add('sort-mode');
        instructions.style.display = 'block';
        sortIcon.className = 'fas fa-save';
        sortText.textContent = 'Desactivar';
        
        alert('🎯 MODO ORDENAR ACTIVADO\n\nPrueba cambiar los números en los campos de orden.\nEl drag & drop estará disponible pronto.');
    } else {
        container.classList.remove('sort-mode');
        instructions.style.display = 'none';
        sortIcon.className = 'fas fa-sort';
        sortText.textContent = 'Modo Ordenar';
    }
}

// Upload de imágenes (simplificado)
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('📤 Subiendo imágenes...');
    
    const formData = new FormData(this);
    const uploadBtn = document.getElementById('upload-btn');
    
    uploadBtn.disabled = true;
    uploadBtn.textContent = 'Subiendo...';
    
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            window.location.reload();
        } else {
            alert('❌ ' + (data.message || 'Error al subir'));
        }
    })
    .catch(error => {
        alert('❌ Error: ' + error.message);
    })
    .finally(() => {
        uploadBtn.disabled = false;
        uploadBtn.textContent = 'Subir Imágenes';
    });
});

function deleteImage(imageId) {
    alert('🗑️ Eliminar imagen pendiente de implementar');
}

// Función de prueba que puedes llamar desde la consola del navegador
window.testGallery = function() {
    console.log('🧪 Ejecutando pruebas...');
    testConnection();
};

console.log('💡 TIP: Puedes ejecutar testGallery() en la consola del navegador para probar la conexión');
</script>
@endpush