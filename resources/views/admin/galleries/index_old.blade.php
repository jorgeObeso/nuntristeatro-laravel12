@extends('admin.layouts.app')

@section('title', 'Gestión de Galerías')

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
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-images"></i>
                        Lista de Galerías
                    </h3>
                </div>
                <div class="card-body">
                    @if($galleries->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="60">ID</th>
                                        <th>Imagen</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th width="100">Imágenes</th>
                                        <th width="80">Estado</th>
                                        <th width="80">Web</th>
                                        <th width="60">Orden</th>
                                        <th width="150">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($galleries as $gallery)
                                        <tr>
                                            <td>{{ $gallery->id }}</td>
                                            <td>
                                                @if($gallery->imagen_portada)
                                                    <img src="{{ asset('storage/' . $gallery->imagen_portada) }}" 
                                                         alt="{{ $gallery->nombre }}"
                                                         class="img-thumbnail"
                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" 
                                                         style="width: 60px; height: 60px; border-radius: 4px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $gallery->nombre }}</strong><br>
                                                <small class="text-muted">Slug: {{ $gallery->slug }}</small>
                                            </td>
                                            <td>
                                                {{ Str::limit($gallery->descripcion, 60) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-info">
                                                    {{ $gallery->images_count }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($gallery->activa)
                                                    <span class="badge badge-success">Activa</span>
                                                @else
                                                    <span class="badge badge-secondary">Inactiva</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($gallery->visible_web)
                                                    <span class="badge badge-success">Visible</span>
                                                @else
                                                    <span class="badge badge-warning">Oculta</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $gallery->orden }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.galleries.show', $gallery) }}" 
                                                       class="btn btn-sm btn-info" 
                                                       title="Ver imágenes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.galleries.edit', $gallery) }}" 
                                                       class="btn btn-sm btn-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.galleries.destroy', $gallery) }}" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('¿Estás seguro de eliminar esta galería? Esta acción no se puede deshacer.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-danger" 
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {{ $galleries->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <h4>No hay galerías creadas</h4>
                            <p class="text-muted">Crea tu primera galería para comenzar a organizar tus imágenes.</p>
                            <a href="{{ route('admin.galleries.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Galería
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection