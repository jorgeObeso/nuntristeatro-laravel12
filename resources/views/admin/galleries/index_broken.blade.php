@extends('admin.layouts.app')

@section('title', 'Galerías')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Galerías</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Galería
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images me-2"></i>
                        Lista de Galerías
                    </h3>
                    <div class="card-tools">
                        <span class="badge bg-info">{{ $galleries->total() }} galerías en total</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($galleries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nombre y Descripción</th>
                                        <th width="100" class="text-center">Imágenes</th>
                                        <th width="100" class="text-center">Estado</th>
                                        <th width="100" class="text-center">Creada</th>
                                        <th width="150" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($galleries as $gallery)
                                        <tr>
                                            <td>
                                                <div class="gallery-info">
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('admin.galleries.show', $gallery) }}" 
                                                           class="text-decoration-none"
                                                           title="Gestionar imágenes de esta galería">
                                                            {{ $gallery->nombre }}
                                                        </a>
                                                    </h6>
                                                    @if($gallery->descripcion)
                                                        <p class="text-muted small mb-0">
                                                            {{ Str::limit($gallery->descripcion, 100) }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    {{ $gallery->images_count }} fotos
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge {{ $gallery->activa ? 'bg-success' : 'bg-secondary' }}">
                                                    <i class="fas {{ $gallery->activa ? 'fa-eye' : 'fa-eye-slash' }} me-1"></i>
                                                    {{ $gallery->activa ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <small class="text-muted">
                                                    {{ $gallery->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Editar Galería">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete({{ $gallery->id }}, '{{ $gallery->nombre }}')"
                                                            title="Eliminar Galería">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        @if($galleries->hasPages())
                            <div class="card-footer">
                                {{ $galleries->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay galerías creadas</h4>
                            <p class="text-muted">Comienza creando tu primera galería de imágenes.</p>
                            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Crear Primera Galería
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar la galería <strong id="gallery-name"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta acción eliminará también todas las imágenes asociadas y no se puede deshacer.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar Galería</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .gallery-info h6 a {
        color: #495057;
        transition: color 0.2s ease;
        font-weight: 600;
    }
    
    .gallery-info h6 a:hover {
        color: #007bff;
        text-decoration: underline !important;
    }
    
    .btn-group .btn {
        border-radius: 4px;
        margin: 0 2px;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.4em 0.6em;
    }
    
    /* Indicar que el nombre es clickeable */
    .gallery-info h6 a::after {
        content: " 📁";
        opacity: 0;
        transition: opacity 0.2s ease;
        font-size: 0.8em;
    }
    
    .gallery-info h6:hover a::after {
        opacity: 1;
    }
</style>
@endsection

@section('scripts')
<script>
    function confirmDelete(galleryId, galleryName) {
        console.log('🗑️ Intentando eliminar galería:', galleryId, galleryName);
        
        document.getElementById('gallery-name').textContent = galleryName;
        // Corregir la URL para que incluya el prefijo admin
        const deleteUrl = '{{ route("admin.galleries.destroy", ":id") }}'.replace(':id', galleryId);
        document.getElementById('delete-form').action = deleteUrl;
        
        console.log('📡 URL de eliminación:', deleteUrl);
        
        // Bootstrap 5 compatible
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Auto-cerrar alertas después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endsection