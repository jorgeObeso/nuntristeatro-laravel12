/**
 * ADMIN MENUS - GESTIÓN DE MENÚS CON DRAG & DROP
 * ===============================================
 * 
 * Sistema de gestión de menús jerárquicos con:
 * - Drag and drop para reordenar menús padre
 * - Movimiento automático de submenús con su padre
 * - Actualización visual en tiempo real
 * - Eliminación con confirmación modal
 * 
 * @requires jQuery
 * @requires SortableJS
 * @version 1.0
 */

$(document).ready(function() {
    'use strict';
    
    console.log('🎯 Inicializando sistema de gestión de menús...');
    
    // ==========================================
    // VARIABLES GLOBALES
    // ==========================================
    let isDragging = false;
    let draggedElement = null;
    let originalChildren = [];
    
    // ==========================================
    // FUNCIONES AUXILIARES
    // ==========================================
    
    /**
     * Obtiene todos los submenús de un menú padre
     * @param {HTMLElement} parentRow - Fila del menú padre
     * @returns {Array} Array de elementos de submenús
     */
    function getMenuChildren(parentRow) {
        const children = [];
        let nextRow = parentRow.nextElementSibling;
        
        while (nextRow && nextRow.classList.contains('submenu-row')) {
            children.push(nextRow);
            nextRow = nextRow.nextElementSibling;
        }
        
        return children;
    }
    
    /**
     * Obtiene todos los elementos de un menú (padre + hijos)
     * @param {HTMLElement} parentRow - Fila del menú padre
     * @returns {Array} Array con padre e hijos
     */
    function getMenuGroup(parentRow) {
        const group = [parentRow];
        const children = getMenuChildren(parentRow);
        return group.concat(children);
    }
    
    /**
     * Remueve temporalmente los submenús del DOM
     * @param {HTMLElement} parentRow - Fila del menú padre
     * @returns {Array} Array de elementos removidos
     */
    function detachChildren(parentRow) {
        const children = getMenuChildren(parentRow);
        children.forEach(child => {
            child.remove();
        });
        return children;
    }
    
    /**
     * Reattacha los submenús después del padre
     * @param {HTMLElement} parentRow - Fila del menú padre
     * @param {Array} children - Array de elementos hijos
     */
    function reattachChildren(parentRow, children) {
        let insertAfter = parentRow;
        children.forEach(child => {
            insertAfter.insertAdjacentElement('afterend', child);
            insertAfter = child;
        });
    }
    
    // ==========================================
    // CONFIGURACIÓN DRAG AND DROP
    // ==========================================
    
    const sortableElement = document.getElementById('sortable-menus');
    if (!sortableElement) {
        console.warn('⚠️ Elemento sortable-menus no encontrado');
        return;
    }
    
    const sortable = Sortable.create(sortableElement, {
        handle: '.handle',
        animation: 200,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        fallbackTolerance: 3,
        
        // Permitir arrastrar tanto menús padre como submenús
        // (eliminamos la función filter que bloqueaba los submenús)
        
        onChoose: function(evt) {
            console.log('🎯 Elemento seleccionado:', evt.item.getAttribute('data-menu-id'));
            
            isDragging = true;
            draggedElement = evt.item;
            
            if (evt.item.classList.contains('submenu-row')) {
                // Es un submenú
                console.log('📄 Arrastrando submenú');
                originalChildren = []; // No hay hijos que mover
            } else {
                // Es un menú padre
                console.log('📁 Arrastrando menú padre');
                
                // Guardar referencia de los hijos originales
                originalChildren = getMenuChildren(draggedElement);
                
                // Marcar visualmente los submenús que se moverán
                originalChildren.forEach(child => {
                    child.classList.add('moving-with-parent');
                });
            }
        },
        
        onStart: function(evt) {
            console.log('🚀 Iniciando arrastre...');
            
            // Ocultar temporalmente los submenús durante el arrastre
            if (originalChildren.length > 0) {
                originalChildren.forEach(child => {
                    child.style.display = 'none';
                });
            }
        },
        
        onMove: function(evt) {
            const draggedItem = evt.dragged;
            const relatedItem = evt.related;
            
            if (draggedItem.classList.contains('submenu-row')) {
                // Arrastrando un submenú
                const draggedParentId = draggedItem.getAttribute('data-parent-id');
                
                if (relatedItem.classList.contains('submenu-row')) {
                    // Intentando mover submenú sobre otro submenú
                    const relatedParentId = relatedItem.getAttribute('data-parent-id');
                    
                    // Solo permitir si pertenecen al mismo padre
                    if (draggedParentId === relatedParentId) {
                        console.log('✅ Reordenando submenús del mismo padre');
                        return true;
                    } else {
                        console.log('❌ No se puede mover submenú a diferente grupo padre');
                        return false;
                    }
                } else {
                    // Intentando mover submenú sobre menú padre - no permitir
                    console.log('❌ No se puede mover submenú fuera de su grupo');
                    return false;
                }
            } else {
                // Arrastrando un menú padre
                if (relatedItem.classList.contains('submenu-row')) {
                    // No permitir soltar menú padre sobre submenú
                    console.log('❌ No se puede mover menú padre sobre submenú');
                    return false;
                } else {
                    // Permitir mover menú padre sobre otro menú padre
                    console.log('✅ Reordenando menús padre');
                    return true;
                }
            }
        },
        
        onEnd: function(evt) {
            console.log('🏁 Finalizando arrastre...');
            console.log(`📊 Movimiento: ${evt.oldIndex} → ${evt.newIndex}`);
            
            isDragging = false;
            
            // Limpiar clases visuales
            document.querySelectorAll('.moving-with-parent').forEach(el => {
                el.classList.remove('moving-with-parent');
                el.style.display = '';
            });
            
            // Verificar si hubo cambio real de posición
            if (evt.oldIndex !== evt.newIndex) {
                console.log('✅ Posición cambió - Procesando actualización...');
                
                // Reattachar submenús en nueva posición
                if (originalChildren.length > 0) {
                    console.log(`🔗 Reattachando ${originalChildren.length} submenús`);
                    reattachChildren(draggedElement, originalChildren);
                }
                
                // Procesar nuevo orden
                processMenuOrder();
            } else {
                console.log('⚠️ Sin cambio de posición detectado');
                
                // Aún así reattachar por seguridad
                if (originalChildren.length > 0) {
                    reattachChildren(draggedElement, originalChildren);
                }
            }
            
            // Limpiar variables
            originalChildren = [];
            draggedElement = null;
        }
    });
    
    // ==========================================
    // PROCESAMIENTO DE ORDEN
    // ==========================================
    
    /**
     * Procesa el nuevo orden de los menús y envía al servidor
     */
    function processMenuOrder() {
        console.log('⚙️ Procesando nuevo orden...');
        
        setTimeout(() => {
            const updates = [];
            const tbody = document.getElementById('sortable-menus');
            
            if (!tbody) {
                console.error('❌ tbody sortable-menus no encontrado');
                return;
            }
            
            const menuRows = tbody.querySelectorAll('tr.menu-row');
            console.log(`📋 Procesando ${menuRows.length} menús padre`);
            
            menuRows.forEach((parentRow, parentIndex) => {
                const menuId = parseInt(parentRow.getAttribute('data-menu-id'));
                const newOrder = parentIndex + 1;
                
                // Actualizar orden del menú padre
                updates.push({
                    id: menuId,
                    orden: newOrder,
                    parent_id: null
                });
                
                console.log(`📁 Menú padre ID:${menuId} → orden:${newOrder}`);
                
                // Procesar submenús
                const childRows = getMenuChildren(parentRow);
                childRows.forEach((childRow, childIndex) => {
                    const childId = parseInt(childRow.getAttribute('data-menu-id'));
                    
                    updates.push({
                        id: childId,
                        orden: childIndex + 1,
                        parent_id: menuId
                    });
                    
                    console.log(`  📄 Submenú ID:${childId} → orden:${childIndex + 1} parent:${menuId}`);
                });
            });
            
            if (updates.length > 0) {
                updateMenuStructure(updates);
            } else {
                console.warn('⚠️ No se generaron actualizaciones');
            }
        }, 200);
    }
    
    /**
     * Envía las actualizaciones de orden al servidor
     * @param {Array} updates - Array de actualizaciones
     */
    function updateMenuStructure(updates) {
        console.log('📤 Enviando actualizaciones al servidor...');
        console.table(updates);
        
        $.ajax({
            url: window.routes?.menuUpdateOrder || '/admin/menus/update-order',
            method: 'POST',
            data: {
                menus: updates,
                _token: window.csrfToken || $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                console.log('⏳ Enviando datos...');
            },
            success: function(response) {
                console.log('✅ Respuesta exitosa:', response);
                
                if (response && response.success) {
                    console.log('🎉 Menús reordenados correctamente');
                    updateOrderDisplay();
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error en actualización:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                alert('Error al actualizar el orden de menús. La página se recargará.');
                
                setTimeout(() => {
                    console.log('🔄 Recargando página...');
                    location.reload();
                }, 2000);
            }
        });
    }
    
    /**
     * Actualiza la visualización de números de orden en la interfaz
     */
    function updateOrderDisplay() {
        console.log('🎨 Actualizando visualización...');
        
        const tbody = document.getElementById('sortable-menus');
        const menuRows = tbody.querySelectorAll('tr.menu-row');
        
        menuRows.forEach((parentRow, index) => {
            const newOrder = index + 1;
            
            // Actualizar número de orden del padre
            const orderNumber = parentRow.querySelector('.order-number');
            if (orderNumber) {
                orderNumber.textContent = newOrder;
                console.log(`📝 Padre ID:${parentRow.getAttribute('data-menu-id')} → orden:${newOrder}`);
            }
            
            // Actualizar números de orden de hijos
            const childRows = getMenuChildren(parentRow);
            childRows.forEach((childRow, childIndex) => {
                const submenuOrderNumber = childRow.querySelector('.submenu-order-number');
                if (submenuOrderNumber) {
                    const submenuOrder = childIndex + 1;
                    submenuOrderNumber.textContent = submenuOrder;
                    
                    console.log(`  📝 Hijo ID:${childRow.getAttribute('data-menu-id')} → orden:${submenuOrder}`);
                }
            });
        });
        
        console.log('✨ Visualización actualizada');
    }
    
    // ==========================================
    // GESTIÓN DE ELIMINACIÓN
    // ==========================================
    
    /**
     * Maneja el click en botón eliminar
     */
    $(document).on('click', '.btn-eliminar', function(e) {
        e.preventDefault();
        console.log('🗑️ Solicitando eliminación...');
        
        const menuId = $(this).data('menu-id');
        console.log('🎯 Menu ID:', menuId);
        
        if (!menuId) {
            console.error('❌ Menu ID no encontrado');
            alert('Error: No se pudo identificar el menú a eliminar');
            return;
        }
        
        const deleteUrl = (window.routes?.menuIndex || '/admin/menus') + '/' + menuId;
        console.log('🔗 URL eliminación:', deleteUrl);
        
        // Configurar formulario
        const deleteForm = $('#deleteForm');
        if (deleteForm.length === 0) {
            console.error('❌ Formulario de eliminación no encontrado');
            alert('Error: Sistema de eliminación no disponible');
            return;
        }
        
        deleteForm.attr('action', deleteUrl);
        
        // Mostrar modal
        const deleteModal = $('#deleteModal');
        if (deleteModal.length === 0) {
            console.error('❌ Modal de eliminación no encontrado');
            alert('Error: Modal de confirmación no disponible');
            return;
        }
        
        deleteModal.modal('show');
        console.log('✅ Modal mostrado');
    });
    
    /**
     * Maneja la confirmación de eliminación
     */
    $('#confirmarEliminar').on('click', function() {
        console.log('💥 Confirmando eliminación...');
        
        const form = $('#deleteForm');
        const action = form.attr('action');
        
        if (!action || action === '') {
            console.error('❌ URL de acción no configurada');
            alert('Error: No se pudo configurar la eliminación');
            return;
        }
        
        console.log('📤 Enviando eliminación:', action);
        form.submit();
    });
    
    // ==========================================
    // INICIALIZACIÓN
    // ==========================================
    
    // Procesar estructura inicial para debug
    if (window.console && console.table) {
        console.log('🔍 Analizando estructura inicial...');
        processMenuOrder();
    }
    
    console.log('🎉 Sistema de gestión de menús inicializado');
});