const { createApp, ref, onMounted, watch, nextTick, computed } = Vue;

const TemplateBuilder = {
    setup() {
        // --- State ---
        // Initialize reactive state
        // Check for globals from Edit view or defaults
        const initialHtml = (typeof TEMPLATE_INITIAL_DATA !== 'undefined' && TEMPLATE_INITIAL_DATA.html_pattern) 
            ? TEMPLATE_INITIAL_DATA.html_pattern 
            : '';
            
        const initialConfigRaw = (typeof TEMPLATE_INITIAL_DATA !== 'undefined' && TEMPLATE_INITIAL_DATA.form_config) 
            ? TEMPLATE_INITIAL_DATA.form_config 
            : '[]';
            
        let parsedConfig = [];
        try {
            parsedConfig = typeof initialConfigRaw === 'string' ? JSON.parse(initialConfigRaw) : initialConfigRaw;
        } catch (e) {
            console.error('Failed to parse initial config', e);
            parsedConfig = [];
        }

        // Separate Settings from Variables
        const defaultLayout = {
            paperSize: 'A4',
            orientation: 'portrait',
            margins: { top: 25, right: 25, bottom: 25, left: 25 }
        };
        
        let initialLayout = { ...defaultLayout };
        const settingsIndex = parsedConfig.findIndex(item => item.variable === '_global_settings');
        
        if (settingsIndex > -1) {
            const settingsItem = parsedConfig[settingsIndex];
            if (settingsItem.layout) {
                initialLayout = { ...defaultLayout, ...settingsItem.layout };
            }
            // Remove settings from the variables list
            parsedConfig.splice(settingsIndex, 1);
        }

        const docContent = ref(initialHtml);
        const formConfig = ref(parsedConfig);
        const layoutSettings = ref(initialLayout);
        const activeTab = ref('variables'); // variables | layout

        const activeVariable = ref(null); // The variable currently being edited/added
        const editingOriginalName = ref(null); // Track original name when editing
        const isModalOpen = ref(false);
        
        // Site URL for fonts
        const siteUrl = typeof SITE_URL !== 'undefined' ? SITE_URL : '/';

        // --- Helpers ---
        const slugify = (text) => {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '_')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '_')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        };

        // --- Modal & Action Logic ---
        const openVariableModal = (selection) => {
            const safeSelection = selection ? selection.trim() : 'variable';
            activeVariable.value = {
                name: slugify(safeSelection),
                label: safeSelection,
                type: 'text' // Default type
            };
            editingOriginalName.value = null; // New mode
            isModalOpen.value = true;
        };

        const editVariable = (variable) => {
            activeVariable.value = {
                name: variable.variable,
                label: variable.label,
                type: variable.type
            };
            editingOriginalName.value = variable.variable; // Edit mode
            isModalOpen.value = true;
        };

        const deleteVariable = (variable) => {
            if (!confirm(\`Are you sure you want to delete the variable "{{ \${variable.variable} }}"? This will remove the configuration, but you must manually remove {{ \${variable.variable} }} from the document.\`)) {
                return;
            }
            formConfig.value = formConfig.value.filter(v => v.variable !== variable.variable);
        };

        const closeModal = () => {
            isModalOpen.value = false;
            activeVariable.value = null;
            editingOriginalName.value = null;
        };

        const saveVariable = () => {
            if (!activeVariable.value) return;

            const newVar = { ...activeVariable.value };
            
            if (editingOriginalName.value) {
                // UPDATE EXISTING
                const index = formConfig.value.findIndex(f => f.variable === editingOriginalName.value);
                if (index > -1) {
                    formConfig.value[index] = {
                        variable: newVar.name,
                        label: newVar.label,
                        type: newVar.type
                    };
                }
            } else {
                // CREATE NEW
                // Check if exists to avoid duplicates (simple check)
                const exists = formConfig.value.some(f => f.variable === newVar.name);
                if (exists) {
                     alert('A variable with this name already exists.');
                     return;
                }

                formConfig.value.push({
                    variable: newVar.name,
                    label: newVar.label,
                    type: newVar.type
                });

                // Insert into Editor ONLY for new variables
                if (tinymce.activeEditor) {
                    tinymce.activeEditor.execCommand('mceInsertContent', false, `{{${newVar.name}}}`);
                }
            }

            closeModal();
        };

        const saveTemplate = () => {
            // Sync current editor content
            if (tinymce.activeEditor) {
                docContent.value = tinymce.activeEditor.getContent();
            }

            // Populate hidden fields
            const configInput = document.querySelector('#form_config');
            const htmlInput = document.querySelector('#html_pattern');
            const form = document.querySelector('form');

            if (configInput && htmlInput && form) {
                // Combine variables and settings
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
                console.error("Form elements not found for submission");
                alert("Error: Could not save template. Form elements missing.");
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
                'F4': { w: 215, h: 330 }, // F4/Folio typical size
            };
            
            let size = sizes[settings.paperSize] || sizes['A4'];
            let w = size.w;
            let h = size.h;
            
            if (settings.orientation === 'landscape') {
                [w, h] = [h, w];
            }

            // Style HTML to look like a desk
            editor.dom.setStyles(html, {
                'background-color': '#e5e7eb', // gray-200
                'height': '100%',
                'display': 'flex',
                'justify-content': 'center',
                'padding': '2rem 0'
            });

            // Style Body to look like paper
            editor.dom.setStyles(body, {
                'width': `${w}mm`,
                'min-height': `${h}mm`,
                'padding': `${settings.margins.top}mm ${settings.margins.right}mm ${settings.margins.bottom}mm ${settings.margins.left}mm`,
                'margin': '0', // Center handled by flex on HTML
                'background-color': '#fff',
                'box-shadow': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
                'box-sizing': 'border-box'
            });
        };

        watch(layoutSettings, (newVal) => {
            if (tinymce.activeEditor) {
                applyLayoutStyles(tinymce.activeEditor, newVal);
            }
        }, { deep: true });

        // --- TinyMCE Initialization ---
        const initTinyMCE = () => {
            if (typeof tinymce === 'undefined') {
                console.error("TinyMCE is not loaded!");
                return;
            }

            tinymce.init({
                selector: '#doc-editor',
                height: '100%', // Take full height of container
                menubar: 'file edit view insert format tools table help',
                plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl | makevar',
                toolbar_sticky: true,
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: true,
                importcss_append: true,
                template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
                template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
                image_caption: true,
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                noneditable_noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                contextmenu: 'link image imagetools table',
                
                // Styling to match PDF output (Bookman Old Style)
                content_style: `
                    @font-face { font-family: 'Bookman Old Style'; src: url('${siteUrl}assets/BOOKOS.TTF') format('truetype'); font-weight: normal; } 
                    @font-face { font-family: 'Bookman Old Style'; src: url('${siteUrl}assets/BOOKOSB.TTF') format('truetype'); font-weight: bold; } 
                    body { font-family: 'Bookman Old Style', serif; font-size:12pt; margin: 2cm; } 
                    table { width: 100% !important; border-collapse: collapse; } 
                    td, th { border: 1px solid #000; padding: 4px; vertical-align: top; }
                `,
                
                setup: (editor) => {
                    // Custom Button: Make Variable
                    editor.ui.registry.addButton('makevar', {
                        text: 'Make Variable',
                        icon: 'code-sample',
                        tooltip: 'Convert selection to variable',
                        onAction: () => {
                            const selection = editor.selection.getContent({ format: 'text' });
                            if (!selection) {
                                alert("Please select some text to convert into a variable.");
                                return;
                            }
                            openVariableModal(selection);
                        }
                    });

                    editor.on('init', () => {
                        editor.setContent(docContent.value);
                    });

                    // Sync content
                    editor.on('change keyup', () => {
                        docContent.value = editor.getContent();
                    });
                }
            });
        };

        // --- Watchers & Sync ---
        const updateExport = () => {
            if (!window.TemplateBuilderExport) window.TemplateBuilderExport = {};
            
            // 1. Export HTML
            window.TemplateBuilderExport.html_pattern = docContent.value;

            // 2. Export Config (Merge Variables + Settings)
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

        // Watch for any changes to sync immediately
        watch([docContent, formConfig, layoutSettings], () => {
            updateExport();
        }, { deep: true });
        
        // Initialize globals immediately
        updateExport();

        // --- Lifecycle ---
        onMounted(() => {
            // Delay slightly to ensure DOM is ready
            setTimeout(() => {
                initTinyMCE();
            }, 100);
        });

        return {
            docContent,
            formConfig,
            layoutSettings,
            activeTab,
            activeVariable,
            isModalOpen,
            closeModal,
            saveVariable,
            editVariable,
            deleteVariable,
            saveTemplate
        };
    },
    template: `
        <div class="h-full flex flex-col bg-white rounded-lg shadow overflow-hidden relative">
            <!-- Header -->
            <div class="bg-gray-800 p-4 border-b border-gray-700 flex justify-between items-center text-white shrink-0">
                <div class="flex items-center space-x-3">
                    <h2 class="text-lg font-bold">Template Editor</h2>
                    <span class="px-2 py-0.5 bg-blue-900 text-blue-200 text-xs rounded border border-blue-700">WYSIWYG</span>
                </div>
                <div class="text-xs text-gray-400">
                    {{ formConfig.length }} Variables
                </div>
            </div>
            
            <!-- Main Content Area (Split View) -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Left: Editor Area -->
                <div class="flex-1 relative bg-gray-100 flex flex-col">
                    <textarea id="doc-editor" class="w-full h-full opacity-0"></textarea>
                </div>

                <!-- Right: Sidebar (Variable Manager) -->
                <div class="w-80 bg-gray-50 border-l border-gray-200 flex flex-col shrink-0 z-10 shadow-lg">
                    <!-- Sidebar Header with Tabs -->
                    <div class="bg-white border-b border-gray-200 flex flex-col shadow-sm">
                        <!-- Tab Switcher -->
                        <div class="flex px-1 pt-2">
                            <button @click="activeTab = 'variables'" 
                                class="flex-1 py-2 text-sm font-medium text-center border-b-2 transition-colors relative"
                                :class="activeTab === 'variables' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                                Variables
                                <span v-if="formConfig.length > 0" class="ml-1 text-[10px] bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded-full">{{ formConfig.length }}</span>
                            </button>
                            <button @click="activeTab = 'layout'" 
                                class="flex-1 py-2 text-sm font-medium text-center border-b-2 transition-colors"
                                :class="activeTab === 'layout' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50'">
                                Layout
                            </button>
                        </div>
                    </div>

                    <!-- Sidebar Content -->
                    <div class="flex-1 overflow-y-auto p-3 bg-gray-50">
                        
                        <!-- Variables Tab -->
                        <div v-show="activeTab === 'variables'" class="space-y-2">
                            <div v-if="formConfig.length === 0" class="text-center py-8 text-gray-400 text-sm italic">
                                No variables yet.<br>Select text in the editor and click "Make Variable".
                            </div>

                            <div v-for="(v, index) in formConfig" :key="v.variable + index" 
                                class="bg-white p-3 rounded border border-gray-200 hover:shadow-sm transition-shadow group flex flex-col relative">
                                
                                <div class="flex justify-between items-start mb-1">
                                    <div class="font-medium text-gray-800 text-sm truncate mr-2" :title="v.label">{{ v.label }}</div>
                                    <span class="text-[10px] uppercase font-bold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded border border-gray-200 shrink-0">
                                        {{ v.type }}
                                    </span>
                                </div>
                                
                                <code class="text-xs text-blue-600 font-mono mb-2 bg-blue-50 px-1 py-0.5 rounded self-start">
                                    {{ '{{' + v.variable + '}}' }}
                                </code>

                                <!-- Actions -->
                                <div class="flex items-center justify-end space-x-2 border-t border-gray-100 pt-2 mt-1">
                                    <button @click="editVariable(v)" class="text-gray-400 hover:text-blue-600 p-1 rounded hover:bg-blue-50 transition-colors" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button @click="deleteVariable(v)" class="text-gray-400 hover:text-red-600 p-1 rounded hover:bg-red-50 transition-colors" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Layout Tab -->
                        <div v-show="activeTab === 'layout'" class="space-y-4">
                            <!-- Paper Size -->
                            <div class="bg-white p-3 rounded border border-gray-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Paper Size</label>
                                <select v-model="layoutSettings.paperSize" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 bg-white">
                                    <option value="A4">A4 (210mm x 297mm)</option>
                                    <option value="F4">F4 (215mm x 330mm)</option>
                                </select>
                            </div>

                            <!-- Orientation -->
                            <div class="bg-white p-3 rounded border border-gray-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Orientation</label>
                                <div class="flex space-x-2">
                                    <button @click="layoutSettings.orientation = 'portrait'" 
                                        :class="['flex-1 py-2 text-sm border rounded flex items-center justify-center space-x-1', 
                                        layoutSettings.orientation === 'portrait' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50']">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span>Portrait</span>
                                    </button>
                                    <button @click="layoutSettings.orientation = 'landscape'" 
                                        :class="['flex-1 py-2 text-sm border rounded flex items-center justify-center space-x-1', 
                                        layoutSettings.orientation === 'landscape' ? 'bg-blue-50 border-blue-500 text-blue-700' : 'border-gray-300 text-gray-600 hover:bg-gray-50']">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span>Landscape</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Margins -->
                            <div class="bg-white p-3 rounded border border-gray-200">
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Margins (mm)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] text-gray-400 uppercase tracking-wide mb-1">Top</label>
                                        <div class="relative">
                                            <input type="number" v-model.number="layoutSettings.margins.top" class="w-full pl-2 pr-6 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <span class="absolute right-2 top-1.5 text-xs text-gray-400">mm</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-gray-400 uppercase tracking-wide mb-1">Right</label>
                                        <div class="relative">
                                            <input type="number" v-model.number="layoutSettings.margins.right" class="w-full pl-2 pr-6 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <span class="absolute right-2 top-1.5 text-xs text-gray-400">mm</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-gray-400 uppercase tracking-wide mb-1">Bottom</label>
                                        <div class="relative">
                                            <input type="number" v-model.number="layoutSettings.margins.bottom" class="w-full pl-2 pr-6 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <span class="absolute right-2 top-1.5 text-xs text-gray-400">mm</span>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] text-gray-400 uppercase tracking-wide mb-1">Left</label>
                                        <div class="relative">
                                            <input type="number" v-model.number="layoutSettings.margins.left" class="w-full pl-2 pr-6 py-1 text-sm border border-gray-300 rounded focus:ring-1 focus:ring-blue-500">
                                            <span class="absolute right-2 top-1.5 text-xs text-gray-400">mm</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div v-if="isModalOpen" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 transform transition-all">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">
                        {{ editingOriginalName ? 'Edit Variable' : 'Create Magic Variable' }}
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Variable Name (System)</label>
                            <input type="text" v-model="activeVariable.name" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono text-sm bg-gray-50">
                            <p class="text-xs text-gray-500 mt-1">Used in code: <code>{{'{{' + activeVariable.name + '}}'}}</code></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Label (Display)</label>
                            <input type="text" v-model="activeVariable.label" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Input Type</label>
                            <select v-model="activeVariable.type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="text">Text (Single Line)</option>
                                <option value="textarea">Text Area (Multi Line)</option>
                                <option value="date">Date</option>
                                <option value="number">Number</option>
                                <option value="richtext">Rich Text</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button @click="closeModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition-colors">
                            Cancel
                        </button>
                        <button @click="saveVariable" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors font-medium">
                            {{ editingOriginalName ? 'Save Changes' : 'Create Variable' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `
};

createApp(TemplateBuilder).mount('#template-builder-app');
