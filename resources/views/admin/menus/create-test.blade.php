@extends('admin.layouts.app')

@section('title', 'Test Crear Menú')

@section('content')
<div class="container-fluid">
    <h1>TEST - Crear Menú</h1>
    
    <form>
        <div class="form-group">
            <label>Tipo de Enlace:</label>
            <select name="tipo_enlace" id="tipo_enlace" class="form-control">
                <option value="">Seleccione</option>
                <option value="contenido">Link a contenido</option>
                <option value="url_externa">URL externa</option>
                <option value="ninguno">Sin enlace</option>
            </select>
        </div>
        
        <div class="content-config" id="contenido-config" style="display:none;">
            <div class="form-group">
                <label>Tipo de Contenido:</label>
                <select name="tipo_contenido_id" id="tipo_contenido_id" class="form-control">
                    <option value="">Seleccione</option>
                    <option value="1">Páginas</option>
                    <option value="3">Noticias</option>
                    <option value="4">Eventos</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Contenido Específico:</label>
                <select name="content_id" id="content_id" class="form-control">
                    <option value="">Seleccione primero un tipo</option>
                </select>
            </div>
        </div>
        
        <div class="content-config" id="url-config" style="display:none;">
            <div class="form-group">
                <label>URL:</label>
                <input type="text" name="url" class="form-control" placeholder="https://...">
            </div>
        </div>
    </form>
</div>
@stop

@push('scripts')
<script src="{{ asset('js/menu-create.js') }}"></script>
@endpush