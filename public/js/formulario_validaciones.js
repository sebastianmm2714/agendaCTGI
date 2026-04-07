/**
 * Agenda CTGI - Validaciones del Formulario de Agenda (Sincronizado)
 */

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('agenda-form');
    if (!form) return;

    // ─── 1. TRANSFORMACIONES EN TIEMPO REAL ───
    const uppercaseFields = ['entidad_empresa', 'contacto', 'objetivo_desplazamiento', 'regional', 'centro'];
    uppercaseFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
                clearError(this);
            });
        }
    });

    // Limpiar errores al interactuar
    form.addEventListener('input', (e) => {
        if (e.target.matches('input, select, textarea')) clearError(e.target);
    });

    form.addEventListener('change', (e) => {
        if (e.target.matches('input, select, textarea')) clearError(e.target);
    });

    // ─── 2. FUNCIONES DE UTILIDAD PARA ERRORES ───
    function showError(element, message) {
        let target = element;
        let insertionPoint = element;

        // Casos especiales (Radio buttons, Selects custom)
        if (element.name === 'clasificacion_id') {
            insertionPoint = element.closest('.card-body');
            target = null; // No aplicamos clase is-invalid al radio oculto
        } else if (element.classList.contains('input-depto')) {
            const item = element.closest('.destino-item');
            target = item.querySelector('.destino-trigger');
            insertionPoint = item.querySelector('.destino-wrapper');
        }

        if (target) target.classList.add('is-invalid-custom');

        const errorId = `error-${element.id || element.name || Math.random()}`;
        let errorDiv = insertionPoint.parentNode.querySelector(`.invalid-feedback-custom[data-for="${errorId}"]`);
        
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback-custom';
            errorDiv.dataset.for = errorId;
            insertionPoint.parentNode.insertBefore(errorDiv, insertionPoint.nextSibling);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    function clearError(element) {
        let target = element;
        let insertionPoint = element;

        if (element.name === 'clasificacion_id') {
            insertionPoint = element.closest('.card-body');
            target = null;
        } else if (element.classList.contains('input-depto')) {
            const item = element.closest('.destino-item');
            target = item.querySelector('.destino-trigger');
            insertionPoint = item.querySelector('.destino-wrapper');
        }

        if (target) target.classList.remove('is-invalid-custom');
        
        const parent = insertionPoint.parentNode;
        const errorDivs = parent.querySelectorAll('.invalid-feedback-custom');
        errorDivs.forEach(div => {
            div.style.display = 'none';
            div.textContent = '';
        });
    }

    function clearAllErrors() {
        form.querySelectorAll('.is-invalid-custom').forEach(el => el.classList.remove('is-invalid-custom'));
        form.querySelectorAll('.invalid-feedback-custom').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
    }

    // ─── 3. VALIDACIÓN AL ENVIAR ───
    form.addEventListener('submit', function (e) {
        clearAllErrors();
        let hasErrors = false;
        let firstErrorElement = null;

        function validate(condition, element, message) {
            if (condition) {
                showError(element, message);
                hasErrors = true;
                if (!firstErrorElement) firstErrorElement = element;
            }
        }

        // Clasificación
        const clasificacion = document.querySelector('input[name="clasificacion_id"]:checked');
        validate(!clasificacion, document.querySelector('input[name="clasificacion_id"]'), 'Seleccione una clasificación.');

        // Destinos (Valida que haya al menos uno y todos tengan departamento)
        const destinos = document.querySelectorAll('.destino-item');
        if (destinos.length === 0) {
            hasErrors = true;
            alert('Debe agregar al menos un destino.');
        } else {
            destinos.forEach((item, index) => {
                const deptoId = item.querySelector('.input-depto');
                validate(!deptoId.value, deptoId, `Seleccione el destino ${index + 1}.`);
            });
        }

        // Fechas
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');
        validate(!fechaInicio.value, fechaInicio, 'Ponga la fecha de inicio.');
        validate(!fechaFin.value, fechaFin, 'Ponga la fecha de regreso.');

        // Otros campos
        const fields = [
            { name: 'entidad_empresa', msg: 'Ponga la empresa.' },
            { name: 'contacto', msg: 'Ponga el contacto.' },
            { name: 'objetivo_desplazamiento', msg: 'Ponga el objetivo.' },
            { name: 'regional', msg: 'Ponga la regional.' },
            { name: 'centro', msg: 'Ponga el centro.' }
        ];

        fields.forEach(f => {
            const el = document.querySelector(`[name="${f.name}"]`);
            if (el) validate(!el.value.trim(), el, f.msg);
        });

        // Obligaciones (Checkbox)
        const obligaciones = document.querySelectorAll('input[name="obligaciones[]"]:checked');
        if (obligaciones.length === 0) {
            const container = document.getElementById('obligaciones-container');
            hasErrors = true;
            // Alerta simple o mensaje inline
            if (!firstErrorElement) firstErrorElement = container;
            const err = document.createElement('div');
            err.className = 'invalid-feedback-custom d-block mt-2';
            err.textContent = 'Debe seleccionar al menos una obligación.';
            container.appendChild(err);
        }

        if (hasErrors) {
            e.preventDefault();
            if (firstErrorElement) {
                let scrollTarget = firstErrorElement;
                if (firstErrorElement.classList?.contains('input-depto')) {
                    scrollTarget = firstErrorElement.closest('.destino-item');
                }
                scrollTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });

    // Sincronización de fechas min en el cliente
    $('#fecha_inicio').on('change', function() {
        $('#fecha_fin').attr('min', this.value);
    });
});
