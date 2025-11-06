// Configuración personalizada de TinyMCE para Eunomia CMS
// Incluye soporte para YouTube, imágenes, tablas y formato completo

// Función para inicializar TinyMCE
window.initTinyMCE = function() {
    // Verificar si TinyMCE ya está cargado
    if (typeof tinymce === 'undefined') {
        console.error('TinyMCE no está cargado');
        return;
    }

    // Destruir instancias existentes antes de inicializar nuevas
    tinymce.remove('.tinymce-editor, .tinymce-simple');
    // TinyMCE para contenido completo
    tinymce.init({
        selector: '.tinymce-editor',
        height: 500,
        menubar: 'file edit view insert format tools table help',
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons',
            'template', 'codesample', 'hr', 'pagebreak', 'nonbreaking'
        ],
        toolbar1: 'undo redo | bold italic underline strikethrough | fontfamily fontsize blocks | forecolor backcolor | emoticons',
        toolbar2: 'alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist | link unlink anchor | image media | table hr pagebreak | insertdatetime charmap | code fullscreen preview',
        block_formats: 'Párrafo=p; Título 1=h1; Título 2=h2; Título 3=h3; Título 4=h4; Título 5=h5; Título 6=h6; Preformateado=pre; Cita=blockquote',
        fontsize_formats: "8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 22pt 24pt 26pt 28pt 36pt 48pt 72pt",
        font_family_formats: 'Arial=arial,helvetica,sans-serif; Georgia=georgia,palatino,serif; Helvetica=helvetica; Times New Roman=times new roman,times,serif; Verdana=verdana,geneva,sans-serif',
        
        // Configuración para imágenes
        image_advtab: true,
        image_uploadtab: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: '/admin/upload-image', // Endpoint que crearemos
        images_upload_credentials: true,
        images_upload_handler: function (blobInfo, success, failure) {
            let xhr, formData;
            xhr = new XMLHttpRequest();
            xhr.withCredentials = false;
            xhr.open('POST', '/admin/upload-image');
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            xhr.onload = function() {
                let json;
                if (xhr.status != 200) {
                    failure('HTTP Error: ' + xhr.status);
                    return;
                }
                json = JSON.parse(xhr.responseText);
                if (!json || typeof json.location != 'string') {
                    failure('Invalid JSON: ' + xhr.responseText);
                    return;
                }
                success(json.location);
            };
            
            formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            xhr.send(formData);
        },
        
        // Configuración para videos de YouTube
        media_live_embeds: true,
        media_url_resolver: function (data, resolve) {
            if (data.url.indexOf('youtube.com/watch') !== -1 || data.url.indexOf('youtu.be/') !== -1) {
                let videoId = '';
                if (data.url.indexOf('youtube.com/watch') !== -1) {
                    videoId = data.url.split('v=')[1];
                    if (videoId.indexOf('&') !== -1) {
                        videoId = videoId.split('&')[0];
                    }
                } else if (data.url.indexOf('youtu.be/') !== -1) {
                    videoId = data.url.split('youtu.be/')[1];
                    if (videoId.indexOf('?') !== -1) {
                        videoId = videoId.split('?')[0];
                    }
                }
                
                resolve({
                    html: '<iframe width="560" height="315" src="https://www.youtube.com/embed/' + videoId + '" frameborder="0" allowfullscreen></iframe>'
                });
            } else {
                resolve({html: ''});
            }
        },
        
        // Configuración de tablas
        table_default_attributes: {
            'border': '0',
            'class': 'table table-bordered'
        },
        table_default_styles: {
            'width': '100%'
        },
        
        // Plantillas predefinidas para teatro
        templates: [
            {
                title: 'Noticia Teatral',
                description: 'Plantilla para noticias de teatro',
                content: '<h2>Título de la Noticia</h2><p><strong>Fecha:</strong> [Fecha del evento]</p><p><strong>Lugar:</strong> [Teatro/Ubicación]</p><p>[Contenido de la noticia...]</p><blockquote><p>"[Cita destacada]"</p></blockquote><p>[Más información...]</p>'
            },
            {
                title: 'Entrevista',
                description: 'Plantilla para entrevistas',
                content: '<h2>[Nombre del Entrevistado]</h2><h3>Entrevista</h3><p><strong>P:</strong> [Pregunta]</p><p><strong>R:</strong> [Respuesta]</p><p><strong>P:</strong> [Pregunta]</p><p><strong>R:</strong> [Respuesta]</p>'
            },
            {
                title: 'Información de Obra',
                description: 'Plantilla para información de obras teatrales',
                content: '<h2>[Título de la Obra]</h2><table class="table table-bordered"><tr><td><strong>Director:</strong></td><td>[Nombre del director]</td></tr><tr><td><strong>Autor:</strong></td><td>[Nombre del autor]</td></tr><tr><td><strong>Género:</strong></td><td>[Drama, Comedia, etc.]</td></tr><tr><td><strong>Duración:</strong></td><td>[Duración aproximada]</td></tr></table><h3>Sinopsis</h3><p>[Descripción de la obra...]</p><h3>Reparto</h3><ul><li>[Actor 1] - [Personaje]</li><li>[Actor 2] - [Personaje]</li></ul>'
            }
        ],
        
        content_style: `
            body { 
                font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Liberation Sans",sans-serif; 
                font-size: 14px;
                line-height: 1.6;
                color: #333;
            }
            .table { border-collapse: collapse; width: 100%; margin-bottom: 1rem; }
            .table td, .table th { padding: 0.5rem; border: 1px solid #dee2e6; }
            .table-bordered { border: 1px solid #dee2e6; }
            blockquote { 
                border-left: 4px solid #007bff; 
                margin: 1rem 0; 
                padding: 0.5rem 1rem; 
                background-color: #f8f9fa; 
            }
        `,
        
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });

    // TinyMCE para resumen (versión simple)
    tinymce.init({
        selector: '.tinymce-simple',
        height: 150,
        menubar: false,
        plugins: ['lists', 'link', 'charmap', 'emoticons'],
        toolbar: 'bold italic underline | bullist numlist | link unlink | emoticons | removeformat',
        content_style: 'body { font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif; font-size: 14px }',
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
};

// Inicializar TinyMCE cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    if (typeof tinymce !== 'undefined') {
        initTinyMCE();
    } else {
        console.error('TinyMCE no está disponible');
    }
});

// También inicializar cuando jQuery esté listo (por compatibilidad con AdminLTE)
if (typeof $ !== 'undefined') {
    $(document).ready(function() {
        if (typeof tinymce !== 'undefined') {
            initTinyMCE();
        }
    });
}