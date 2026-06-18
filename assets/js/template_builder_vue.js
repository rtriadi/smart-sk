(function() {

const { createApp, ref, onMounted, watch, nextTick, computed } = Vue;

const TemplateBuilder = {
    setup() {
        // --- State ---
        const initialHtml = (typeof TEMPLATE_INITIAL_DATA !== 'undefined' && TEMPLATE_INITIAL_DATA.html_pattern) 
            ? TEMPLATE_INITIAL_DATA.html_pattern 
            : '';
        
        // Default HTML template for new SK
        const defaultHtmlTemplate = `<div style="text-align: center; margin-bottom: 20px;">
<p style="font-size: 12pt; font-weight: bold; margin: 0;">KEPUTUSAN</p>
<p style="font-size: 12pt; font-weight: bold; margin: 0;">{{jabatan_penandatangan}}</p>
<p style="font-size: 12pt; margin: 5px 0;">NOMOR: {{no_sk}}</p>
<p style="font-size: 12pt; font-weight: bold; margin: 10px 0;">TENTANG</p>
<p style="font-size: 12pt; font-weight: bold; margin: 0;">{{judul_sk}}</p>
</div>

<p style="text-align: center; font-weight: bold; margin: 20px 0;">{{jabatan_penandatangan}}</p>

<table style="width: 100%; border: none; margin-bottom: 15px;">
<tbody>
<tr>
<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0;">Menimbang</td>
<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>
<td style="vertical-align: top; border: none; padding: 5px 0;">
<ol type="a" style="margin: 0; padding-left: 20px;">
{{#each list_menimbang}}<li>{{this}}</li>{{/each}}
</ol>
</td>
</tr>
</tbody>
</table>

<table style="width: 100%; border: none; margin-bottom: 15px;">
<tbody>
<tr>
<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0;">Mengingat</td>
<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>
<td style="vertical-align: top; border: none; padding: 5px 0;">
<ol type="1" style="margin: 0; padding-left: 20px;">
{{#each list_mengingat}}<li>{{this}}</li>{{/each}}
</ol>
</td>
</tr>
</tbody>
</table>

<table style="width: 100%; border: none; margin-bottom: 15px;">
<tbody>
<tr>
<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0;">Memperhatikan</td>
<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>
<td style="vertical-align: top; border: none; padding: 5px 0;">
<ol type="1" style="margin: 0; padding-left: 20px;">
{{#each list_memperhatikan}}<li>{{this}}</li>{{/each}}
</ol>
</td>
</tr>
</tbody>
</table>

<p style="text-align: center; font-weight: bold; margin: 20px 0;">MEMUTUSKAN:</p>

<table style="width: 100%; border: none; margin-bottom: 20px;">
<tbody>
<tr>
<td style="width: 120px; vertical-align: top; border: none; padding: 5px 0;">Menetapkan</td>
<td style="width: 20px; vertical-align: top; border: none; padding: 5px 0;">:</td>
<td style="vertical-align: top; border: none; padding: 5px 0;">{{isi_memutuskan}}</td>
</tr>
</tbody>
</table>

<div style="margin-top: 40px;">
<table style="width: 100%; border: none;">
<tbody>
<tr>
<td style="width: 50%; border: none;"></td>
<td style="text-align: center; border: none;">
<p style="margin: 0;">Ditetapkan di {{tempat_penetapan}}</p>
<p style="margin: 0;">Pada tanggal {{tanggal_indo}}</p>
<p style="margin: 30px 0 5px 0; font-weight: bold;">{{jabatan_penandatangan}},</p>
<p style="margin: 60px 0 5px 0; font-weight: bold; text-decoration: underline;">{{nama_penandatangan}}</p>
<p style="margin: 0;">NIP. {{nip_penandatangan}}</p>
</td>
</tr>
</tbody>
</table>
</div>`;
            
        const initialConfigRaw = (typeof TEMPLATE_INITIAL_DATA !== 'undefined' && TEMPLATE_INITIAL_DATA.form_config) 
            ? TEMPLATE_INITIAL_DATA.form_config 
            : '[]';
            
        // Default variables commonly used in SK documents
        const defaultVariables = [
            { variable: 'no_sk', label: 'Nomor SK', type: 'text' },
            { variable: 'tanggal_sk', label: 'Tanggal SK', type: 'date' },
            { variable: 'judul_sk', label: 'Judul/Tentang SK', type: 'textarea' },
            { variable: 'list_menimbang', label: 'Menimbang (Poin-poin)', type: 'repeater' },
            { variable: 'list_mengingat', label: 'Mengingat (Dasar Hukum)', type: 'repeater' },
            { variable: 'list_memperhatikan', label: 'Memperhatikan', type: 'repeater' },
            { variable: 'isi_memutuskan', label: 'Isi Memutuskan/Menetapkan', type: 'textarea' },
            { variable: 'nama_penandatangan', label: 'Nama Penandatangan', type: 'text' },
            { variable: 'jabatan_penandatangan', label: 'Jabatan Penandatangan', type: 'text' },
            { variable: 'nip_penandatangan', label: 'NIP Penandatangan', type: 'text' },
            { variable: 'tempat_penetapan', label: 'Tempat Penetapan', type: 'text' },
            { variable: 'tanggal_hijri', label: 'Tanggal Hijriah', type: 'text' },
        ];

        let parsedConfig = [];
        try {
            parsedConfig = typeof initialConfigRaw === 'string' ? JSON.parse(initialConfigRaw) : initialConfigRaw;
        } catch (e) {
            console.error('Failed to parse initial config', e);
            parsedConfig = [];
        }

        // Separate Settings from Variables
        // Load default layout from Data Master (localStorage) if available
        let dataMasterSettings = null;
        try {
            const saved = localStorage.getItem('sk_editor_settings');
            if (saved) {
                dataMasterSettings = JSON.parse(saved);
            }
        } catch (e) {
            console.warn('Could not load Data Master settings:', e);
        }

        // Default layout - use Data Master if available, otherwise hardcoded defaults
        const defaultLayout = {
            paperSize: dataMasterSettings?.paperSize || 'A4',
            orientation: dataMasterSettings?.orientation || 'portrait',
            margins: { 
                top: dataMasterSettings?.marginTop || 20, 
                right: dataMasterSettings?.marginRight || 20, 
                bottom: dataMasterSettings?.marginBottom || 20, 
                left: dataMasterSettings?.marginLeft || 20 
            }
        };
        
        let initialLayout = { ...defaultLayout };
        const settingsIndex = parsedConfig.findIndex(item => item.variable === '_global_settings');
        
        if (settingsIndex > -1) {
            const settingsItem = parsedConfig[settingsIndex];
            if (settingsItem.layout) {
                initialLayout = { ...defaultLayout, ...settingsItem.layout };
            }
            parsedConfig.splice(settingsIndex, 1);
        }
        
        // Check if this is a NEW template (no existing config or empty)
        const isNewTemplate = !parsedConfig || parsedConfig.length === 0 || 
            (parsedConfig.length === 1 && parsedConfig[0].variable === '_global_settings');
        
        if (isNewTemplate) {
            // Add default variables for new templates
            parsedConfig = [...defaultVariables];
        }

        // Use default HTML if this is a new template with no content
        const useDefaultHtml = isNewTemplate && !initialHtml.trim();
        const docContent = ref(useDefaultHtml ? defaultHtmlTemplate : initialHtml);
        const formConfig = ref(parsedConfig);
        const layoutSettings = ref(initialLayout);
        const activeTab = ref('variables');

        const activeVariable = ref(null);
        const editingOriginalName = ref(null);
        const isModalOpen = ref(false);
        const isAddingFromSidebar = ref(false);
        
        const siteUrl = typeof SITE_URL !== 'undefined' ? SITE_URL : '/';

        // --- Helpers ---
        const slugify = (text) => {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '_')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '_')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        };

        // --- Modal & Action Logic ---
        const openVariableModal = (selection) => {
            const safeSelection = selection ? selection.trim() : 'variable';
            activeVariable.value = {
                name: slugify(safeSelection),
                label: safeSelection,
                type: 'text'
            };
            editingOriginalName.value = null;
            isAddingFromSidebar.value = false;
            isModalOpen.value = true;
        };

        const addVariableFromSidebar = () => {
            activeVariable.value = {
                name: 'new_variable',
                label: 'Variabel Baru',
                type: 'text'
            };
            editingOriginalName.value = null;
            isAddingFromSidebar.value = true;
            isModalOpen.value = true;
        };

        const editVariable = (variable) => {
            activeVariable.value = {
                name: variable.variable,
                label: variable.label,
                type: variable.type
            };
            editingOriginalName.value = variable.variable;
            isAddingFromSidebar.value = false;
            isModalOpen.value = true;
        };

        const deleteVariable = (variable) => {
            const varTag = '{{' + variable.variable + '}}';
            if (!confirm('Hapus variabel "' + varTag + '"?')) {
                return;
            }
            formConfig.value = formConfig.value.filter(v => v.variable !== variable.variable);
        };

        const closeModal = () => {
            isModalOpen.value = false;
            activeVariable.value = null;
            editingOriginalName.value = null;
            isAddingFromSidebar.value = false;
        };

        const saveVariable = () => {
            if (!activeVariable.value) return;

            const newVar = { ...activeVariable.value };
            
            if (!newVar.name.trim()) {
                alert('Nama variabel harus diisi!');
                return;
            }
            if (!newVar.label.trim()) {
                alert('Label variabel harus diisi!');
                return;
            }
            
            if (editingOriginalName.value) {
                const index = formConfig.value.findIndex(f => f.variable === editingOriginalName.value);
                if (index > -1) {
                    formConfig.value[index] = {
                        variable: newVar.name,
                        label: newVar.label,
                        type: newVar.type
                    };
                }
            } else {
                const exists = formConfig.value.some(f => f.variable === newVar.name);
                if (exists) {
                     alert('Variabel dengan nama ini sudah ada.');
                     return;
                }

                formConfig.value.push({
                    variable: newVar.name,
                    label: newVar.label,
                    type: newVar.type
                });

                if (tinymce.activeEditor) {
                    const tag = '{{' + newVar.name + '}}';
                    tinymce.activeEditor.execCommand('mceInsertContent', false, tag);
                }
            }

            closeModal();
        };

        const copyVariableTag = (variable) => {
            const tag = '{{' + variable.variable + '}}';
            navigator.clipboard.writeText(tag).then(() => {
                alert('Tag disalin: ' + tag);
            }).catch(() => {
                alert('Gagal menyalin tag');
            });
        };

        const insertVariableAtCursor = (variable) => {
            if (tinymce.activeEditor) {
                const tag = '{{' + variable.variable + '}}';
                tinymce.activeEditor.execCommand('mceInsertContent', false, tag);
                tinymce.activeEditor.focus();
            }
        };

        const saveTemplate = () => {
            if (tinymce.activeEditor) {
                docContent.value = tinymce.activeEditor.getContent();
            }

            const configInput = document.querySelector('#form_config');
            const htmlInput = document.querySelector('#html_pattern');
            const form = document.querySelector('form');

            if (configInput && htmlInput && form) {
                const finalConfig = [
                    ...formConfig.value,
                    {
                        type: 'settings',
                        variable: '_global_settings',
                        label: 'Global Settings',
                        layout: { ...layoutSettings.value }
                    }
                ];

                configInput.value = JSON.stringify(finalConfig);
                htmlInput.value = docContent.value;
                form.submit();
            } else {
                console.error("Form elements not found");
                alert("Error: Could not save template.");
            }
        };

        // --- Layout Logic ---
        const applyLayoutStyles = (editor, settings) => {
            if (!editor || !editor.dom) return;
            
            const body = editor.getBody();
            const doc = editor.getDoc();
            const html = doc.documentElement;

            const sizes = {
                'A4': { w: 210, h: 297 },
                'F4': { w: 215, h: 330 },
            };
            
            let size = sizes[settings.paperSize] || sizes['A4'];
            let w = size.w;
            let h = size.h;
            
            if (settings.orientation === 'landscape') {
                [w, h] = [h, w];
            }

            // Calculate content height for page simulation
            const pageHeightMm = h - settings.margins.top - settings.margins.bottom;

            editor.dom.setStyles(html, {
                'background-color': '#525659',
                'min-height': '100%',
                'padding': '20px 0'
            });

            editor.dom.setStyles(body, {
                'width': w + 'mm',
                'min-height': h + 'mm',
                'padding': settings.margins.top + 'mm ' + settings.margins.right + 'mm ' + settings.margins.bottom + 'mm ' + settings.margins.left + 'mm',
                'margin': '20px auto',
                'background-color': '#fff',
                'box-shadow': '0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2)',
                'box-sizing': 'border-box',
                'position': 'relative'
            });

            // Add page break indicator styles
            const styleId = 'template-builder-page-styles';
            let styleEl = doc.getElementById(styleId);
            if (!styleEl) {
                styleEl = doc.createElement('style');
                styleEl.id = styleId;
                doc.head.appendChild(styleEl);
            }
            
            // CSS for page break visualization and continuous pages
            styleEl.textContent = 
                '/* Page break styling */' +
                '.mce-pagebreak {' +
                '    display: block !important;' +
                '    width: 100% !important;' +
                '    height: 40px !important;' +
                '    margin: 20px -' + settings.margins.left + 'mm !important;' +
                '    padding: ' + settings.margins.bottom + 'mm 0 ' + settings.margins.top + 'mm 0 !important;' +
                '    background: linear-gradient(to bottom, #fff 0%, #fff 30%, #525659 30%, #525659 70%, #fff 70%, #fff 100%) !important;' +
                '    border: none !important;' +
                '    box-shadow: inset 0 10px 15px -10px rgba(0,0,0,0.2), inset 0 -10px 15px -10px rgba(0,0,0,0.2) !important;' +
                '    position: relative !important;' +
                '    page-break-after: always !important;' +
                '}' +
                '.mce-pagebreak::before {' +
                '    content: "— Halaman Baru —" !important;' +
                '    position: absolute !important;' +
                '    top: 50% !important;' +
                '    left: 50% !important;' +
                '    transform: translate(-50%, -50%) !important;' +
                '    background: #525659 !important;' +
                '    color: #9ca3af !important;' +
                '    padding: 2px 12px !important;' +
                '    font-size: 10px !important;' +
                '    border-radius: 10px !important;' +
                '    white-space: nowrap !important;' +
                '}' +
                '/* Continuous page effect */' +
                'body {' +
                '    background: repeating-linear-gradient(' +
                '        to bottom,' +
                '        #fff 0,' +
                '        #fff ' + h + 'mm,' +
                '        #e5e7eb ' + h + 'mm,' +
                '        #e5e7eb calc(' + h + 'mm + 20px)' +
                '    ) !important;' +
                '}';
        };

        watch(layoutSettings, (newVal) => {
            if (tinymce.activeEditor) {
                applyLayoutStyles(tinymce.activeEditor, newVal);
            }
        }, { deep: true });

        // --- TinyMCE Initialization ---
        const initTinyMCE = () => {
            if (typeof tinymce === 'undefined') {
                return;
            }

            const fontUrl1 = siteUrl + 'assets/BOOKOS.TTF';
            const fontUrl2 = siteUrl + 'assets/BOOKOSB.TTF';
            const contentStyle = 
                "@font-face { font-family: 'Bookman Old Style'; src: url('" + fontUrl1 + "') format('truetype'); font-weight: normal; } " +
                "@font-face { font-family: 'Bookman Old Style'; src: url('" + fontUrl2 + "') format('truetype'); font-weight: bold; } " +
                "body { font-family: 'Bookman Old Style', serif; font-size: 12pt; line-height: 1.5; } " +
                "table { width: 100% !important; border-collapse: collapse; } " +
                "td, th { border: 1px solid #000; padding: 4px; vertical-align: top; } " +
                "p { margin: 0 0 0.5em 0; } " +
                "/* Page break visual indicator */ " +
                ".mce-pagebreak { display: block; height: 2px; border: 0; border-top: 2px dashed #0d9488; margin: 1em 0; page-break-after: always; }";

            tinymce.init({
                selector: '#doc-editor',
                height: '100%',
                min_height: 600,
                menubar: 'file edit view insert format tools table help',
                plugins: 'print preview paste searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount help charmap',
                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor backcolor removeformat | newpage | charmap | fullscreen preview print | image link | makevar',
                toolbar_sticky: true,
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                image_advtab: true,
                image_caption: true,
                toolbar_mode: 'sliding',
                contextmenu: 'link image table',
                content_style: contentStyle,
                
                // Page break settings
                pagebreak_separator: '<!-- pagebreak -->',
                pagebreak_split_block: true,
                
                setup: (editor) => {
                    // Custom Button: Make Variable
                    editor.ui.registry.addButton('makevar', {
                        text: 'Make Variable',
                        icon: 'code-sample',
                        tooltip: 'Convert selection to variable',
                        onAction: () => {
                            const selection = editor.selection.getContent({ format: 'text' });
                            if (!selection) {
                                alert("Silakan pilih teks terlebih dahulu.");
                                return;
                            }
                            openVariableModal(selection);
                        }
                    });

                    // Custom Button: New Page (more visible page break)
                    editor.ui.registry.addButton('newpage', {
                        text: 'Halaman Baru',
                        icon: 'page-break',
                        tooltip: 'Sisipkan pemisah halaman baru',
                        onAction: () => {
                            editor.execCommand('mcePageBreak');
                        }
                    });

                    editor.on('init', () => {
                        editor.setContent(docContent.value);
                        applyLayoutStyles(editor, layoutSettings.value);
                    });

                    editor.on('change keyup', () => {
                        docContent.value = editor.getContent();
                    });
                }
            });
        };

        // --- Watchers & Sync ---
        const updateExport = () => {
            if (!window.TemplateBuilderExport) window.TemplateBuilderExport = {};
            
            window.TemplateBuilderExport.html_pattern = docContent.value;

            const finalConfig = [
                ...formConfig.value,
                {
                    type: 'settings',
                    variable: '_global_settings',
                    label: 'Global Settings',
                    layout: { ...layoutSettings.value }
                }
            ];
            window.TemplateBuilderExport.form_config = JSON.stringify(finalConfig);
        };

        watch([docContent, formConfig, layoutSettings], () => {
            updateExport();
        }, { deep: true });
        
        updateExport();

        // --- Lifecycle ---
        onMounted(() => {
            nextTick(() => {
                const editorEl = document.getElementById('doc-editor');
                if (editorEl) {
                    initTinyMCE();
                } else {
                    setTimeout(() => {
                        const retryEl = document.getElementById('doc-editor');
                        if (retryEl) {
                            initTinyMCE();
                        }
                    }, 500);
                }
            });
        });

        return {
            docContent,
            formConfig,
            layoutSettings,
            activeTab,
            activeVariable,
            editingOriginalName,
            isModalOpen,
            isAddingFromSidebar,
            closeModal,
            saveVariable,
            editVariable,
            deleteVariable,
            addVariableFromSidebar,
            copyVariableTag,
            insertVariableAtCursor,
            saveTemplate
        };
    },
    template: `
        <div class="h-full flex flex-col bg-white rounded-lg shadow overflow-hidden relative">
            <!-- Header -->
            <div class="bg-slate-800 p-4 border-b border-slate-700 flex justify-between items-center text-white shrink-0">
                <div class="flex items-center space-x-3">
                    <h2 class="text-lg font-bold">Template Editor</h2>
                    <span class="px-2 py-0.5 bg-teal-900 text-teal-200 text-xs rounded border border-teal-700">WYSIWYG</span>
                </div>
                <div class="text-xs text-gray-400">
                    {{ formConfig.length }} Variables
                </div>
            </div>
            
            <!-- Main Content Area (Split View) -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Left: Editor Area -->
                <div class="flex-1 relative bg-slate-200 flex flex-col">
                    <textarea id="doc-editor" class="w-full h-full opacity-0"></textarea>
                </div>

                <!-- Right: Sidebar -->
                <div class="w-80 bg-slate-50 border-l border-slate-200 flex flex-col shrink-0 z-10 shadow-lg">
                    <!-- Tab Switcher -->
                    <div class="bg-white border-b border-slate-200 flex">
                        <button @click="activeTab = 'variables'" 
                            class="flex-1 py-3 text-sm font-medium text-center border-b-2 transition-colors"
                            :class="activeTab === 'variables' ? 'border-teal-600 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Variables
                            <span v-if="formConfig.length > 0" class="ml-1 text-xs bg-slate-200 text-gray-600 px-1.5 py-0.5 rounded-full">{{ formConfig.length }}</span>
                        </button>
                        <button @click="activeTab = 'layout'" 
                            class="flex-1 py-3 text-sm font-medium text-center border-b-2 transition-colors"
                            :class="activeTab === 'layout' ? 'border-teal-600 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700'">
                            Layout
                        </button>
                    </div>

                    <!-- Sidebar Content -->
                    <div class="flex-1 overflow-y-auto p-3 bg-slate-50">
                        
                        <!-- Variables Tab -->
                        <div v-show="activeTab === 'variables'" class="space-y-3">
                            <!-- Add Variable Button -->
                            <button @click="addVariableFromSidebar" 
                                class="w-full bg-teal-600 hover:bg-teal-700 text-white py-2.5 px-4 rounded-lg font-medium transition-all flex items-center justify-center gap-2 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Variabel
                            </button>

                            <!-- Empty State -->
                            <div v-if="formConfig.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                <p class="font-medium text-slate-500">Belum ada variabel</p>
                                <p class="text-xs text-slate-400 mt-1">Klik tombol di atas atau pilih teks di editor</p>
                            </div>

                            <!-- Variables List -->
                            <div v-for="(v, index) in formConfig" :key="v.variable + index" 
                                class="bg-white p-3 rounded-lg border border-slate-200 hover:shadow-md transition-all">
                                
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-medium text-gray-800 text-sm truncate mr-2">{{ v.label }}</div>
                                    <span class="text-xs uppercase font-bold text-gray-500 bg-slate-100 px-1.5 py-0.5 rounded">
                                        {{ v.type }}
                                    </span>
                                </div>
                                
                                <code class="text-xs text-teal-600 font-mono mb-3 bg-teal-50 px-2 py-1 rounded block border border-teal-100">{<span>{</span>{{ v.variable }}<span>}</span>}</code>

                                <!-- Actions -->
                                <div class="flex items-center justify-between border-t border-slate-100 pt-2 mt-1">
                                    <div class="flex items-center space-x-1">
                                        <button @click="insertVariableAtCursor(v)" class="text-gray-400 hover:text-teal-600 p-1.5 rounded hover:bg-teal-50" title="Insert">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button @click="copyVariableTag(v)" class="text-gray-400 hover:text-blue-600 p-1.5 rounded hover:bg-blue-50" title="Copy">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                        </button>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <button @click="editVariable(v)" class="text-gray-400 hover:text-amber-500 p-1.5 rounded hover:bg-amber-50" title="Edit">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                        </button>
                                        <button @click="deleteVariable(v)" class="text-gray-400 hover:text-red-500 p-1.5 rounded hover:bg-red-50" title="Delete">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Layout Tab -->
                        <div v-show="activeTab === 'layout'" class="space-y-4">
                            <!-- Paper Size -->
                            <div class="bg-white p-4 rounded-lg border border-slate-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ukuran Kertas</label>
                                <select v-model="layoutSettings.paperSize" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg bg-white">
                                    <option value="A4">A4 (210mm x 297mm)</option>
                                    <option value="F4">F4 (215mm x 330mm)</option>
                                </select>
                            </div>

                            <!-- Orientation -->
                            <div class="bg-white p-4 rounded-lg border border-slate-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Orientasi</label>
                                <div class="flex space-x-2">
                                    <button @click="layoutSettings.orientation = 'portrait'" 
                                        :class="['flex-1 py-2.5 text-sm border rounded-lg transition-all', layoutSettings.orientation === 'portrait' ? 'bg-teal-50 border-teal-500 text-teal-700 font-medium' : 'border-slate-300 text-gray-600']">
                                        Portrait
                                    </button>
                                    <button @click="layoutSettings.orientation = 'landscape'" 
                                        :class="['flex-1 py-2.5 text-sm border rounded-lg transition-all', layoutSettings.orientation === 'landscape' ? 'bg-teal-50 border-teal-500 text-teal-700 font-medium' : 'border-slate-300 text-gray-600']">
                                        Landscape
                                    </button>
                                </div>
                            </div>

                            <!-- Margins -->
                            <div class="bg-white p-4 rounded-lg border border-slate-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-3">Margin (mm)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Atas</label>
                                        <input type="number" v-model.number="layoutSettings.margins.top" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Kanan</label>
                                        <input type="number" v-model.number="layoutSettings.margins.right" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Bawah</label>
                                        <input type="number" v-model.number="layoutSettings.margins.bottom" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-400 mb-1">Kiri</label>
                                        <input type="number" v-model.number="layoutSettings.margins.left" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-bold text-gray-800">
                            {{ editingOriginalName ? 'Edit Variabel' : 'Tambah Variabel Baru' }}
                        </h3>
                        <button @click="closeModal" class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-100">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Variabel</label>
                            <input type="text" v-model="activeVariable.name" 
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg font-mono text-sm bg-slate-50">
                            <p class="text-xs text-gray-500 mt-1.5">Tag: <code class="bg-teal-50 text-teal-600 px-1 rounded">{<span>{</span>{{ activeVariable.name }}<span>}</span>}</code></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Label</label>
                            <input type="text" v-model="activeVariable.label" 
                                class="w-full px-4 py-2.5 border border-slate-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Input</label>
                            <select v-model="activeVariable.type" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg bg-white">
                                <option value="text">Text</option>
                                <option value="textarea">Text Area</option>
                                <option value="date">Tanggal</option>
                                <option value="number">Angka</option>
                                <option value="repeater">Repeater (Multi-baris)</option>
                                <option value="richtext">Rich Text</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="closeModal" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium">
                            Batal
                        </button>
                        <button @click="saveVariable" class="px-5 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium">
                            {{ editingOriginalName ? 'Simpan' : 'Tambah' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `
};

// Check if mount target exists
const mountTarget = document.getElementById('template-builder-app');

if (mountTarget) {
    const app = createApp(TemplateBuilder);
    app.mount('#template-builder-app');
} else {
}
})();
