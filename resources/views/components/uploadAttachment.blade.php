{{-- Componente Moderno para Adjuntar Archivos --}}
<div class="mt-2">
    {{-- Input nativo oculto para mantener la estética --}}
    <input type="file" name="attachment" id="attachment" class="d-none" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : ''">
    
    {{-- Botón corporativo que simula el clic en el input de archivo --}}
    <button type="button" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm" onclick="document.getElementById('attachment').click();">
        <i class="fas fa-paperclip me-1"></i> Adjuntar evidencia (Opcional)
    </button>
    
    {{-- Etiqueta para mostrar el nombre del archivo seleccionado --}}
    <span id="file-name" class="ms-2 text-muted small fw-semibold"></span>
</div>