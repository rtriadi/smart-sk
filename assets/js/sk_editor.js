$(document).ready(function () {
    // 1. Initialize Editor
    console.log("Config:", TEMPLATE_CONFIG); // Debug
    renderSidebar(TEMPLATE_CONFIG);
    renderPreview();

    // Delay initial apply to ensure DOM is ready
    setTimeout(applyGlobalSettings, 100);

    // Global Settings Listeners
    $('#global-paper, input[name="orientation"]').change(function () {
        applyGlobalSettings();
    });

    $('#margin-top, #margin-bottom, #margin-left, #margin-right').on('input', function () {
        applyGlobalSettings();
    });

    $('#toggle-kop').change(function () {
        if ($(this).is(':checked')) {
            $('#preview-canvas').removeClass('hide-kop');
        } else {
            $('#preview-canvas').addClass('hide-kop');
        }
    });

    function applyGlobalSettings() {
        let paper = $('#global-paper').val();
        let orientation = $('input[name="orientation"]:checked').val();
        let mt = $('#margin-top').val() + 'mm';
        let mb = $('#margin-bottom').val() + 'mm';
        let ml = $('#margin-left').val() + 'mm';
        let mr = $('#margin-right').val() + 'mm';

        let canvas = $('#preview-canvas');

        // Reset classes
        canvas.removeClass('a4-page f4-page legal-page landscape');

        // Apply Size
        if (paper === 'A4') canvas.addClass('a4-page');
        else if (paper === 'F4') canvas.addClass('f4-page');
        else if (paper === 'Legal') canvas.addClass('legal-page');

        // Apply Orientation
        if (orientation === 'landscape') canvas.addClass('landscape');

        // Apply Margins
        canvas.css({
            'padding-top': mt,
            'padding-bottom': mb,
            'padding-left': ml,
            'padding-right': mr
        });
    }

    // 2. Render Sidebar Inputs
    function renderSidebar(config) {
        let html = '';
        config.forEach(section => {
            html += `<h5 class="mt-3 text-warning border-bottom pb-2">${section.section}</h5>`;
            section.fields.forEach(field => {
                html += buildFieldHtml(field);
            });
        });
        $('#dynamic-form-container').html(html);

        // Initialize Plugins
        // $('.summernote').summernote(); // If using summernote
    }

    function buildFieldHtml(field, index = null) {
        let fieldId = field.variable + (index !== null ? `_${index}` : '');
        let value = field.default || '';

        let html = `<div class="form-group">`;
        html += `<label>${field.label}</label>`;

        if (field.type === 'text') {
            html += `<input type="text" class="form-control live-input" data-var="${field.variable}" id="${fieldId}" value="${value}">`;
        } else if (field.type === 'textarea') {
            html += `<textarea class="form-control live-input" rows="${field.rows || 3}" data-var="${field.variable}" id="${fieldId}">${value}</textarea>`;
        } else if (field.type === 'date') {
            html += `<input type="date" class="form-control live-input" data-var="${field.variable}" id="${fieldId}" value="${value}">`;
        } else if (field.type === 'select') {
            html += `<select class="form-control live-input" data-var="${field.variable}" id="${fieldId}">`;
            if (field.options) {
                field.options.forEach(opt => {
                    html += `<option value="${opt}">${opt}</option>`;
                });
            }
            html += `</select>`;
        } else if (field.type === 'repeater') {
            html += `<div class="repeater-container" data-var="${field.variable}">`;
            html += `<div class="repeater-items"></div>`;
            html += `<button class="btn btn-xs btn-info mt-2 btn-add-row" data-var="${field.variable}"><i class="fas fa-plus"></i> Add Row</button>`;
            html += `</div>`;
        }

        html += `</div>`;
        return html;
    }

    // 3. Live Preview Logic
    $(document).on('input change', '.live-input', function () {
        renderPreview();
    });

    function renderPreview() {
        let html = TEMPLATE_HTML;

        // Simple Replace
        $('.live-input').each(function () {
            let variable = $(this).data('var');
            let value = $(this).val();
            // Regex to replace {{variable}}
            let regex = new RegExp(`{{${variable}}}`, 'g');
            html = html.replace(regex, value);
        });

        // Repeater Logic (Basic List Support)
        $('.repeater-container').each(function () {
            let variable = $(this).data('var');
            let items = [];
            $(this).find('.repeater-input').each(function () {
                items.push($(this).val());
            });

            // Handle {{#each variable}} ... {{/each}}
            // This is a simplified parser. For production, use Handlebars.js
            let loopRegex = new RegExp(`{{#each ${variable}}}([\\s\\S]*?){{/each}}`, 'g');
            html = html.replace(loopRegex, function (match, content) {
                let loopHtml = '';
                items.forEach(item => {
                    loopHtml += content.replace(/{{this}}/g, item);
                });
                return loopHtml;
            });
        });

        $('#preview-canvas').html(html);
    }

    // 4. Repeater Logic
    $(document).on('click', '.btn-add-row', function () {
        let variable = $(this).data('var');
        // Find config for this repeater
        let fieldConfig = findFieldConfig(variable);
        if (fieldConfig) {
            let subField = fieldConfig.fields[0]; // Assume single field repeater for now
            let html = `<div class="repeater-item d-flex mb-2">`;
            html += `<textarea class="form-control repeater-input" rows="2" placeholder="Enter item..."></textarea>`;
            html += `<button class="btn btn-xs btn-danger ml-2 btn-remove-row"><i class="fas fa-times"></i></button>`;
            html += `</div>`;
            $(this).siblings('.repeater-items').append(html);
            renderPreview();
        }
    });

    $(document).on('click', '.btn-remove-row', function () {
        $(this).closest('.repeater-item').remove();
        renderPreview();
    });

    function findFieldConfig(variable) {
        for (let section of TEMPLATE_CONFIG) {
            for (let field of section.fields) {
                if (field.variable === variable) return field;
            }
        }
        return null;
    }

    // 5. Save & Print
    $('#btn-save').click(function () {
        let data = {};
        $('.live-input').each(function () {
            data[$(this).data('var')] = $(this).val();
        });

        // Collect Repeater Data
        $('.repeater-container').each(function () {
            let variable = $(this).data('var');
            let items = [];
            $(this).find('.repeater-input').each(function () {
                items.push($(this).val());
            });
            data[variable] = items;
        });
    });

    $.post(SITE_URL + 'sk_editor/save', {
        data: JSON.stringify(data),
        template_id: TEMPLATE_ID
    }, function (response) {
        let res = JSON.parse(response);
        if (res.status === 'success') {
            alert('Draft Saved! ID: ' + res.id);
            // Store ID for printing
            $('#btn-print').data('id', res.id);
        } else {
            alert('Error saving draft');
        }
    });

    // Print Handler
    $('#btn-print').click(function () {
        let id = $(this).data('id');
        if (!id) {
            alert('Please save the draft first!');
            return;
        }
        window.open(SITE_URL + 'sk_editor/generate_pdf/' + id, '_blank');
    });

}); // End document.ready

