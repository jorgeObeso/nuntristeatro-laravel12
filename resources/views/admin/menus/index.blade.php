@extends('admin.layouts.app')

@section('title', 'Gestión de Menús')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Menús</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Menús</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <!-- Acciones -->
        <div class="row mb-3">
            <div class="col-md-6">
                <a href="{{ route('admin.menus.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Menú
                </a>
            </div>
            <div class="col-md-6 text-right">
                <button id="saveOrder" class="btn btn-success" style="display: none;">
                    <i class="fas fa-save"></i> Guardar Orden
                </button>
            </div>
        </div>

        <!-- Tabla de menús -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Menús</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap" id="menusTable">
                    <thead>
                        <tr>
                            <th width="30">#</th>
                            <th>Orden</th>
                            <th>Título (ES)</th>
                            <th>Enlace</th>
                            <th>Estado</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="sortableMenus">
                        @forelse($menus as $menu)
                            @php
                                $textoEs = $menu->textos->where('idioma.codigo', 'es')->first();
                                $nivel = 0;
                                $parent = $menu;
                                while($parent->parent_id) {
                                    $nivel++;
                                    $parent = $parent->parent;
                                }
                            @endphp
                            <tr data-id="{{ $menu->id }}" data-orden="{{ $menu->orden }}">
                                <td>
                                    <i class="fas fa-grip-vertical handle" style="cursor: move;"></i>
                                </td>
                                <td>
                                    <span class="orden-display">{{ $menu->orden }}</span>
                                    <input type="hidden" class="orden-input" value="{{ $menu->orden }}">
                                </td>
                                <td>
                                    @for($i = 0; $i < $nivel; $i++)
                                        <span class="text-muted">├─</span>
                                    @endfor
                                    {{ $textoEs ? $textoEs->titulo : 'Sin título' }}
                                    @if($menu->parent_id)
                                        <br><small class="text-muted">Submenú de: {{ $menu->parent->textos->where('idioma.codigo', 'es')->first()->titulo ?? 'N/A' }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($menu->enlace)
                                        <a href="{{ $menu->enlace }}" target="{{ $menu->blank ? '_blank' : '_self' }}" class="text-primary">
                                            {{ Str::limit($menu->enlace, 40) }}
                                        </a>
                                        @if($menu->blank)
                                            <i class="fas fa-external-link-alt text-muted ml-1" title="Abre en nueva ventana"></i>
                                        @endif
                                    @else
                                        <span class="text-muted">Sin enlace</span>
                                    @endif
                                </td>
                                <td>
                                    @if($menu->visible)
                                        <span class="badge badge-success">Visible</span>
                                    @else
                                        <span class="badge badge-secondary">Oculto</span>
                                    @endif
                                </td>
                                <td>
                                    @if($menu->parent_id)
                                        <span class="badge badge-info">Submenú</span>
                                    @else
                                        <span class="badge badge-primary">Principal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.menus.show', $menu) }}" 
                                           class="btn btn-sm btn-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.menus.edit', $menu) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.menus.destroy', $menu) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este menú? También se eliminarán sus submenús.')">
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
                                <td colspan="7" class="text-center py-4">
                                    <p class="text-muted mb-0">No se encontraron menús</p>
                                    <a href="{{ route('admin.menus.create') }}" class="btn btn-primary btn-sm mt-2">
                                        <i class="fas fa-plus"></i> Crear primer menú
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información adicional -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-info"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Gestión de Menús</span>
                        <span class="info-box-number">{{ count($menus) }} menús configurados</span>
                        <div class="info-box-more">
                            Puedes arrastrar los elementos para cambiar su orden. Los cambios se guardarán automáticamente.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .handle {
            color: #6c757d;
        }
        .handle:hover {
            color: #007bff;
        }
        .sortable-ghost {
            opacity: 0.5;
            background-color: #f8f9fa;
        }
        .sortable-chosen {
            background-color: #e9ecef;
        }
        .table td {
            vertical-align: middle;
        }
        .btn-group .btn {
            margin-right: 2px;
        }
        .info-box-more {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
@stop

@section('js')
    <!-- SortableJS para ordenar menús -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('sortableMenus');
            const saveButton = document.getElementById('saveOrder');
            
            if (table) {
                const sortable = Sortable.create(table, {
                    handle: '.handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    onEnd: function(evt) {
                        updateOrder();
                        saveButton.style.display = 'inline-block';
                    }
                });
            }

            function updateOrder() {
                const rows = table.querySelectorAll('tr');
                rows.forEach((row, index) => {
                    const ordenDisplay = row.querySelector('.orden-display');
                    const ordenInput = row.querySelector('.orden-input');
                    if (ordenDisplay && ordenInput) {
                        ordenDisplay.textContent = index + 1;
                        ordenInput.value = index + 1;
                    }
                });
            }

            saveButton.addEventListener('click', function() {
                const menusData = [];
                const rows = table.querySelectorAll('tr[data-id]');
                
                rows.forEach((row, index) => {
                    menusData.push({
                        id: row.dataset.id,
                        orden: index + 1
                    });
                });

                fetch('{{ route("admin.menus.update-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        menus: menusData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Orden de menús actualizado correctamente');
                        saveButton.style.display = 'none';
                    } else {
                        toastr.error('Error al actualizar el orden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('Error al actualizar el orden');
                });
            });

            @if(session('success'))
                toastr.success('{{ session('success') }}');
            @endif
        });
    </script>
@stop