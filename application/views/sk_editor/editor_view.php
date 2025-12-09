<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart SK Editor - <?php echo $template->nama_sk; ?></title>
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Vue 3 (CDN) -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script>
        <?php
        // Safely prepare variables with error suppression
        $config = isset($template->form_config) ? @json_decode($template->form_config) : [];
        $config = $config ? $config : []; 

        $draftData = isset($draft_data) ? @json_decode($draft_data) : null;
        
        $draftSettings = isset($draft_settings) ? @json_decode($draft_settings) : null;
        ?>
        
        var TEMPLATE_CONFIG = <?php echo json_encode($config); ?>;
        var TEMPLATE_HTML = <?php echo json_encode($template->html_pattern); ?>;
        var SITE_URL = '<?php echo rtrim(site_url(), "/") . "/"; ?>';
        var TEMPLATE_ID = <?php echo $template->id; ?>;
        var DRAFT_DATA = <?php echo json_encode($draftData); ?>;
        var DRAFT_SETTINGS = <?php echo json_encode($draftSettings); ?>;
        var ARCHIVE_ID = <?php echo $archive_id ? $archive_id : 'null'; ?>;
        var PEJABAT_DATA = <?php echo isset($pejabat) ? json_encode($pejabat) : '[]'; ?>;
    </script>
    
    <style>
        /* Custom Scrollbar for Sidebar */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* A4 Paper Base */
        .paper-preview {
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            min-height: 297mm;
            height: auto;
            margin: 0 auto;
            position: relative;
            transition: all 0.3s ease;
            font-family: "Times New Roman", Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: black;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Ensure tables don't overflow */
        .paper-preview table {
            width: 100%;
            table-layout: fixed;
        }
        .paper-preview td {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Fix List Styles */
        /* List Styles - Robust Fix */
        .paper-preview ul, .paper-preview ol {
            margin: 0 0 0.5em 0;
            padding-left: 2em; /* Use padding for outside markers */
            list-style-position: outside;
        }

        .paper-preview li {
            display: list-item !important; /* Force list-item display */
            margin-bottom: 0.25em;
        }

        /* DOTS for UL */
        .paper-preview ul {
            list-style-type: disc !important;
        }

        /* NUMBERS for OL (default) */
        .paper-preview ol {
            list-style-type: decimal !important;
        }

        /* Specific Type Overrides */
        .paper-preview ol[type="a"], .paper-preview ol.lower-alpha {
            list-style-type: lower-alpha !important;
        }
        .paper-preview ol[type="A"], .paper-preview ol.upper-alpha {
            list-style-type: upper-alpha !important;
        }
        .paper-preview ol[type="i"], .paper-preview ol.lower-roman {
            list-style-type: lower-roman !important;
        }
        .paper-preview ol[type="I"], .paper-preview ol.upper-roman {
            list-style-type: upper-roman !important;
        }

        /* Hide Kop Helper */
        .hide-kop .header-kop {
            display: none !important;
        }

        [v-cloak] { display: none; }

        /* PRINT STYLES */
        @media print {
            @page {
                margin: 0; 
            }
            body {
                background: white;
                height: auto;
                overflow: visible;
                display: block;
            }
            
            /* Hide UI Elements by default */
            #app > div.w-80, /* Sidebar */
            #app > div.bg-white, /* Toolbar if any */
            button,
            a,
            .no-print {
                display: none !important;
            }

            /* Main Content Reset */
            #app {
                display: block !important; 
                height: auto !important;
                overflow: visible !important;
                width: 100% !important;
            }

            .flex-1 {
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
                height: auto !important;
                width: 100% !important;
                background: white !important;
            }

            /* Paper Preview Reset */
            .paper-preview {
                box-shadow: none !important;
                margin: 0 !important;
                width: 100% !important;
                min-height: auto !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-gray-900 h-screen overflow-hidden text-sm font-sans transition-colors duration-200">

<div id="app" v-cloak class="flex h-full">

    <!-- Sidebar (Left) -->
    <div class="w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col shadow-lg z-10 transition-colors duration-200">
        <!-- Header -->
        <div class="h-14 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center px-4 justify-between transition-colors duration-200">
            <div class="flex items-center text-slate-800 dark:text-white font-bold text-lg">
                <i class="fas fa-file-signature text-indigo-600 dark:text-blue-500 mr-2"></i> Smart Editor
            </div>
            <div class="flex items-center space-x-2">
                <!-- Theme Toggle -->
                <button @click="toggleTheme" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-yellow-400 transition" title="Toggle Theme">
                    <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <a href="<?php echo site_url('sk_editor'); ?>" class="text-gray-400 hover:text-red-500 transition ml-2">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-4 space-y-6 scrollbar-hide">
            
            <!-- Global Settings -->
            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600 transition-colors duration-200">
                <h3 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3 flex items-center">
                    <i class="fas fa-cog mr-2"></i> Global Settings
                </h3>
                
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Paper Size</label>
                        <select v-model="globalSettings.paperSize" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                            <option value="A4">A4</option>
                            <option value="F4">F4</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Orientation</label>
                        <select v-model="globalSettings.orientation" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                            <option value="portrait">Portrait</option>
                            <option value="landscape">Landscape</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Margins (mm)</label>
                    <div class="grid grid-cols-4 gap-2">
                        <input type="number" v-model="globalSettings.marginTop" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-1 py-1.5 text-center text-xs focus:border-indigo-500 focus:ring-indigo-500 outline-none" placeholder="T">
                        <input type="number" v-model="globalSettings.marginBottom" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-1 py-1.5 text-center text-xs focus:border-indigo-500 focus:ring-indigo-500 outline-none" placeholder="B">
                        <input type="number" v-model="globalSettings.marginLeft" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-1 py-1.5 text-center text-xs focus:border-indigo-500 focus:ring-indigo-500 outline-none" placeholder="L">
                        <input type="number" v-model="globalSettings.marginRight" class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-1 py-1.5 text-center text-xs focus:border-indigo-500 focus:ring-indigo-500 outline-none" placeholder="R">
                    </div>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-600 dark:text-gray-400 text-xs font-medium">Show Letterhead</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="globalSettings.showKop" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 dark:peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <!-- Kop Settings -->
                <div v-if="globalSettings.showKop" class="space-y-2 border-t border-gray-200 dark:border-gray-600 pt-3">
                    <div class="mb-2">
                        <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1">SK/Draft Logo (Optional Override)</label>
                        <input type="file" @change="handleLogoUpload" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-400 dark:file:bg-blue-900 dark:file:text-blue-200">
                        
                        <!-- Logo Preview & Sizing -->
                        <div v-if="formData.skLogo || globalSettings.kopLogo" class="mt-2 bg-gray-100 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-center mb-2">
                                <img :src="formData.skLogo || globalSettings.kopLogo" class="max-h-20 object-contain border border-gray-300 bg-white">
                            </div>
                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-500">Width:</label>
                                <!-- If skLogo is present, model custom width. Else if forcing override? 
                                     Actually, handleLogoUpload initializes skLogoWidth from kopLogoWidth.
                                     So we can safely bind to skLogoWidth IF skLogo is present?
                                     Or simply bind to skLogoWidth ALWAYS, and ensure it defaults to kopLogoWidth?
                                     Let's bind to formData.skLogoWidth (which we init in Vue if missing) -->
                                <input type="range" v-model="formData.skLogoWidth" min="40" max="250" class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer">
                                <input type="number" v-model="formData.skLogoWidth" class="w-12 text-xs text-center border rounded dark:bg-gray-700 dark:text-white">
                            </div>
                            <div v-if="formData.skLogo" class="text-center mt-1">
                                <button @click="formData.skLogo = null; formData.skLogoWidth = null" class="text-xs text-red-500 hover:underline">Reset to Global</button>
                            </div>
                        </div>
                    </div>
                    <input type="text" v-model="globalSettings.kopTitle1" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs outline-none focus:border-indigo-500" placeholder="Line 1">
                    <input type="text" v-model="globalSettings.kopTitle2" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs outline-none focus:border-indigo-500" placeholder="Line 2">
                    <input type="text" v-model="globalSettings.kopTitle3" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs outline-none focus:border-indigo-500" placeholder="Line 3">
                    <input type="text" v-model="globalSettings.kopTitle4" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs outline-none focus:border-indigo-500" placeholder="Line 4">
                    <textarea v-model="globalSettings.kopAddress" rows="2" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs outline-none focus:border-indigo-500" placeholder="Address"></textarea>
                </div>
            </div>

            <!-- Logo SK (Content - Tengah Atas) -->
            <div class="bg-amber-50 dark:bg-amber-900/30 rounded-lg p-4 border border-amber-100 dark:border-amber-800 transition-colors duration-200 mb-6">
                <h3 class="text-xs font-bold text-amber-700 dark:text-amber-300 uppercase tracking-wider mb-3 flex items-center">
                    <i class="fas fa-image mr-2"></i> Logo Tengah SK
                </h3>
                <div class="mb-2">
                    <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Upload Logo (e.g. Garuda)</label>
                    <input type="file" @change="handleContentLogoUpload" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 dark:text-gray-400 dark:file:bg-amber-900 dark:file:text-amber-200">
                    
                    <div v-if="formData.skContentLogo" class="mt-2 bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                        <div class="flex justify-center mb-2">
                            <!-- Show preview with dynamic width -->
                            <img :src="formData.skContentLogo" class="object-contain" :style="{width: (formData.skContentLogoWidth || 100) + 'px'}">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label class="text-xs text-gray-500">Size:</label>
                            <input type="range" v-model="formData.skContentLogoWidth" min="20" max="300" class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer">
                            <input type="number" v-model="formData.skContentLogoWidth" class="w-12 text-xs text-center border rounded dark:bg-gray-700 dark:text-white">
                        </div>
                         <div class="text-center mt-1">
                            <button @click="formData.skContentLogo = null; formData.skContentLogoWidth = null" class="text-xs text-red-500 hover:underline">Remove Logo</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signatory (Penandatangan) -->
            <div class="bg-indigo-50 dark:bg-indigo-900/30 rounded-lg p-4 border border-indigo-100 dark:border-indigo-800 transition-colors duration-200">
                 <h3 class="text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider mb-3 flex items-center">
                    <i class="fas fa-file-signature mr-2"></i> Penandatangan
                </h3>
                <div class="mb-2">
                    <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Pilih Pejabat</label>
                    <select @change="onPejabatSelect($event)" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition-colors">
                        <option value="">-- Pilih --</option>
                        <option v-for="p in pejabatList" :key="p.id" :value="p.id">{{ p.nama }} ({{ p.jabatan }})</option>
                    </select>
                </div>
            </div>

            <!-- Dynamic Form -->
            <div v-for="(section, sIndex) in config" :key="sIndex" class="transition-colors duration-200">
                <h3 class="text-xs font-bold text-indigo-600 dark:text-blue-400 uppercase tracking-wider mb-3 border-b border-gray-200 dark:border-gray-700 pb-1 flex items-center">
                   <span class="mr-2 px-2 py-0.5 bg-indigo-50 dark:bg-blue-900/30 rounded text-indigo-700 dark:text-blue-300">{{sIndex + 1}}</span> {{ section.section }}
                </h3>
                
                <div class="space-y-4 mb-6">
                    <div v-for="(field, fIndex) in section.fields" :key="fIndex">
                        <label v-if="field.type !== 'checkbox'" class="block text-gray-600 dark:text-gray-300 text-xs mb-1 font-medium">{{ field.label }}</label>
                        
                        <!-- Text/Textarea/Number -->
                        <textarea v-if="['text', 'textarea'].includes(field.type)" v-model="formData[field.variable]" rows="2"
                            :readonly="['nama_penandatangan', 'jabatan_penandatangan'].includes(field.variable)"
                            :class="{'bg-gray-100 dark:bg-gray-700 cursor-not-allowed text-gray-500': ['nama_penandatangan', 'jabatan_penandatangan'].includes(field.variable)}"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm"></textarea>

                        <input v-if="field.type === 'number'" type="number" v-model="formData[field.variable]"
                             :readonly="['nama_penandatangan', 'jabatan_penandatangan'].includes(field.variable)"
                             :class="{'bg-gray-100 dark:bg-gray-700 cursor-not-allowed text-gray-500': ['nama_penandatangan', 'jabatan_penandatangan'].includes(field.variable)}"
                             class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                        
                        <input v-if="field.type === 'date'" type="date" v-model="formData[field.variable]"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                            
                        <!-- Select -->
                        <select v-if="field.type === 'select'" v-model="formData[field.variable]"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                            <option v-for="opt in field.options" :value="opt">{{ opt }}</option>
                        </select>

                        <!-- Checkbox -->
                        <div v-if="field.type === 'checkbox'" class="flex items-center mt-2">
                            <input type="checkbox" v-model="formData[field.variable]" :id="'cb-'+fIndex" 
                                class="w-4 h-4 text-indigo-600 bg-white border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label :for="'cb-'+fIndex" class="ml-2 text-sm font-medium text-slate-700 dark:text-gray-300">{{ field.label }}</label>
                        </div>

                        <!-- Repeater -->
                        <div v-if="field.type === 'repeater'" class="space-y-2">
                            <div v-for="(item, rIndex) in formData[field.variable]" :key="rIndex" class="flex gap-2 items-start">
                                <span class="text-xs text-gray-400 mt-2 w-4 text-right">{{rIndex+1}}.</span>
                                <textarea v-model="formData[field.variable][rIndex]" rows="2"
                                    class="flex-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 outline-none shadow-sm"></textarea>
                                <button @click="removeRepeaterItem(field.variable, rIndex)" class="text-red-400 hover:text-red-600 p-1 mt-1 transition">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <button @click="addRepeaterItem(field.variable)" class="w-full py-1.5 border border-dashed border-indigo-300 dark:border-gray-600 text-indigo-600 dark:text-blue-400 rounded hover:bg-indigo-50 dark:hover:bg-gray-700 hover:border-indigo-400 transition text-xs uppercase font-bold bg-white dark:bg-transparent shadow-sm">
                                <i class="fas fa-plus mr-1"></i> Add Row
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-3 transition-colors duration-200">
            <button @click="saveDraft" :disabled="isSaving" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2 rounded shadow-md font-semibold transition flex items-center justify-center">
                <i class="fas" :class="isSaving ? 'fa-spinner fa-spin' : 'fa-save'"></i> <span class="ml-2">{{ isSaving ? 'Saving...' : 'Save' }}</span>
            </button>
            <button @click="printPdf" class="bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded shadow-md font-semibold transition flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <button @click="exportWord" class="bg-blue-600 hover:bg-blue-700 text-white py-2 rounded shadow-md font-semibold transition flex items-center justify-center">
                <i class="fas fa-file-word mr-2"></i> Word
            </button>
            <button @click="exportPdf" class="bg-red-600 hover:bg-red-700 text-white py-2 rounded shadow-md font-semibold transition flex items-center justify-center">
                <i class="fas fa-file-pdf mr-2"></i> PDF
            </button>
            <!-- Hidden button for ID storage hack -->
            <div id="btn-print-hidden" data-id="" style="display:none;"></div>
        </div>
    </div>

    <!-- Main Content (Preview) -->
    <div class="flex-1 bg-slate-200/50 dark:bg-gray-900 overflow-auto p-8 relative transition-colors duration-200">
        <!-- Canvas -->
        <!-- Canvas -->
        <!-- 1. Hidden Source (Raw HTML) -->
        <div id="raw-content" style="position:absolute; left:-9999px; top:0; width: 210mm; visibility: hidden;">
             <div class="paper-preview" v-html="previewHtml"></div>
        </div>

        <!-- 2. Visible Pagination Container -->
        <div id="pagination-container" class="flex flex-col items-center space-y-8 pb-10">
            <!-- Pages will be injected here by JS -->
        </div>
    </div>

</div>

<!-- Vue Application Logic -->
<script src="<?php echo base_url('assets/js/sk_editor_vue.js'); ?>"></script>
</body>
</html>
