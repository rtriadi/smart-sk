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
    <!-- TinyMCE 5 (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js" referrerpolicy="origin"></script>

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
        var TEMPLATE_PATTERN = <?php echo json_encode($template->nomor_pattern); ?>;
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

        /* Custom Fonts */
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('<?php echo base_url('assets/BOOKOS.TTF'); ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('<?php echo base_url('assets/BOOKOSB.TTF'); ?>') format('truetype');
            font-weight: bold;
            font-style: normal;
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
            font-family: 'Bookman Old Style', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: black;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Ensure tables don't overflow */
        .paper-preview table {
            width: 100%;
            table-layout: auto; /* Changed from fixed to auto for better column sizing */
            border-collapse: collapse;
        }
        .paper-preview td, .paper-preview th {
            word-wrap: break-word;
            overflow-wrap: break-word;
            border: 1px solid #000; /* Force borders */
            padding: 4px; /* Consistent padding */
        }
        /* Except for layout tables (Kop etc) which usually have no border class or specific id */
        /* But here we target ALL tables in preview. This might affect the Header! */
        /* We should scope it to .attachment-content type stuff or force no-border on main layout if needed. */
        /* Wait, the main layout usually uses tables too. */
        /* Strategy: Only apply borders to tables inside .attachment-content or specifically added ones. */
        
        /* Better: Apply general reset, but for user content tables (TinyMCE), they usually come without classes. */
        /* Let's target .attachment-content table */
        .attachment-content table {
             width: 100%;
             border-collapse: collapse;
             margin-bottom: 1em;
        }
        .attachment-content td, .attachment-content th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        /* Fix Layout tables which might be in the main content ? */
        /* If the main content has tables, they might need borders too. */

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

<div id="app" v-cloak class="flex h-full pt-14 md:pt-0">

    <!-- Mobile Header (Visible on small screens) -->
    <div class="md:hidden fixed top-0 w-full z-20 bg-slate-900 border-b border-slate-800 h-14 flex items-center justify-between px-4 transition-colors duration-200">
        <div class="flex items-center text-white font-bold text-lg">
            <button @click="toggleSidebar" class="mr-3 text-slate-300 hover:text-white focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <i class="fas fa-file-signature text-teal-400 mr-2"></i> Smart Editor
        </div>
        <div>
             <!-- Theme Toggle (Mobile) -->
             <button @click="toggleTheme" class="text-slate-400 hover:text-white transition" title="Toggle Theme">
                <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Sidebar Overlay -->
    <div v-show="isSidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 z-20 md:hidden glass-effect transition-opacity"></div>


    <!-- Sidebar (Left) -->
    <div :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-80 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col shadow-lg transform transition-transform duration-300 md:translate-x-0 md:static md:inset-auto ease-in-out">
        <!-- Header -->
        <div class="h-14 bg-slate-900 border-b border-slate-800 flex items-center px-4 justify-between transition-colors duration-200 shrink-0">
            <div class="flex items-center text-white font-bold text-lg">
                <i class="fas fa-file-signature text-teal-400 mr-2"></i> Smart Editor
            </div>
            <div class="flex items-center space-x-3">
                <!-- Theme Toggle -->
                <button @click="toggleTheme" class="text-slate-400 hover:text-white transition" title="Toggle Theme">
                    <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <a href="<?php echo site_url('sk_editor'); ?>" class="flex items-center text-slate-300 hover:text-white transition text-xs font-medium group" title="Back to Dashboard">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> <span>Back to Dashboard</span>
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

                <!-- Typography -->
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Font Size</label>
                        <select v-model="globalSettings.fontSize" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="10pt">10pt</option>
                            <option value="11pt">11pt</option>
                            <option value="12pt">12pt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 text-xs mb-1 font-medium">Line Spacing</label>
                        <select v-model="globalSettings.lineHeight" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none">
                            <option value="1.0">1.0</option>
                            <option value="1.15">1.15</option>
                            <option value="1.5">1.5</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-600 dark:text-gray-400 text-xs font-medium">Show Letterhead</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="globalSettings.showKop" class="sr-only peer">
                        <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600 dark:peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <div class="flex items-center justify-between mb-3">
                    <span class="text-gray-600 dark:text-gray-400 text-xs font-medium">Show Page Numbers</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="globalSettings.showPageNumbers" class="sr-only peer">
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
                    <textarea v-model="globalSettings.kopAddress" rows="3" class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-2 py-1.5 text-xs outline-none focus:border-indigo-500" placeholder="Address"></textarea>
                </div>

                <!-- Attachments (Lampiran) -->
                <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-gray-600 dark:text-gray-400 text-xs font-bold uppercase tracking-wider">Lampiran</label>
                        <button @click="addAttachment" class="text-xs bg-indigo-50 dark:bg-blue-900 text-indigo-600 dark:text-blue-300 px-2 py-1 rounded hover:bg-indigo-100 dark:hover:bg-blue-800 transition-colors">+ Add</button>
                    </div>
                    
                    <div v-if="formData.attachments && formData.attachments.length > 0" class="space-y-3">
                        <div v-for="(att, index) in formData.attachments" :key="index" class="bg-gray-50 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-700 relative">
                             <div class="flex justify-between items-center mb-1">
                                <span class="text-[10px] text-gray-500 font-bold">LAMPIRAN #{{ index + 1 }}</span>
                                <button @click="removeAttachment(index)" class="text-red-500 hover:text-red-700 text-xs">x</button>
                             </div>
                             <input type="text" v-model="att.title" class="w-full mb-1 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded text-xs px-2 py-1 text-gray-900 dark:text-gray-100" placeholder="Judul Lampiran (e.g. Daftar Pegawai)">
                             
                             <!-- TinyMCE Target -->
                             <textarea :id="'attachment-editor-' + index" class="tinymce-editor w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded text-xs px-2 py-1 h-40"></textarea>
                             
                             <div class="text-[10px] text-gray-400 italic mt-1">Gunakan toolbar di atas untuk membuat tabel/list.</div>
                        </div>
                    </div>
                    <div v-else class="text-center py-4 bg-gray-50 dark:bg-gray-800 rounded border border-dashed border-gray-300 dark:border-gray-600">
                        <span class="text-xs text-gray-400">Tidak ada lampiran</span>
                    </div>
                </div>

                <!-- Save Defaults Button -->
                <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                    <button @click="saveAsDefault" class="w-full text-xs bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 py-1.5 rounded transition flex items-center justify-center" title="Save current margins, paper size, and typography as your default for new documents">
                        <i class="fas fa-save mr-2"></i> Save Settings as Default
                    </button>
                </div>
            </div>

                <!-- Page Appearance Settings (New) -->
                <div>
                    <h3 class="text-xs font-bold text-indigo-600 dark:text-blue-400 uppercase tracking-wider mb-3 border-b border-gray-200 dark:border-gray-700 pb-1 flex items-center">
                        <span class="mr-2 px-2 py-0.5 bg-indigo-50 dark:bg-blue-900/30 rounded text-indigo-700 dark:text-blue-300"><i class="fas fa-cog"></i></span> TAMPILAN HALAMAN
                    </h3>
                    
                    <div class="space-y-4 mb-6 px-1">
                        <!-- Show Kop Toggle -->
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Tampilkan Kop Surat</label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="globalSettings.showKop" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        


                        <!-- Logo Tengah SK -->
                        <div class="space-y-2 pt-2 border-t border-dashed border-gray-200 dark:border-gray-700">
                            <label class="text-xs font-medium text-gray-700 dark:text-gray-300">Logo Tengah SK</label>
                            
                            <!-- Upload Logo Tengah -->
                            <input type="file" @change="handleContentLogoUpload" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 dark:text-gray-400 dark:file:bg-green-900 dark:file:text-green-200">
                            
                            <!-- Logo Preview & Sizing -->
                            <div v-if="formData.logo_tengah || formData.skContentLogo" class="bg-gray-100 dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-center mb-2">
                                    <img :src="formData.logo_tengah || formData.skContentLogo" class="max-h-20 object-contain border border-gray-300 bg-white">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-xs text-gray-500">Ukuran:</label>
                                    <input type="range" v-model="formData.logo_tengah_width" min="40" max="300" class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer">
                                    <input type="number" v-model="formData.logo_tengah_width" class="w-12 text-xs text-center border rounded dark:bg-gray-700 dark:text-white">
                                </div>
                                <div class="text-center mt-1">
                                    <span class="text-xs text-gray-500">{{ formData.logo_tengah_width || 100 }}px</span>
                                </div>
                                <div class="text-center mt-1">
                                    <button @click="formData.logo_tengah = null; formData.skContentLogo = null; formData.logo_tengah_width = null" class="text-xs text-red-500 hover:underline">Hapus Logo</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                <!-- Dynamic Form -->
                <!-- Dynamic Form (Accordion) -->
                <div v-for="(section, sIndex) in config" :key="sIndex" class="transition-colors duration-200">
                    <button @click="toggleSection(sIndex)" class="w-full text-left text-xs font-bold text-indigo-600 dark:text-blue-400 uppercase tracking-wider mb-2 border-b border-gray-200 dark:border-gray-700 pb-2 flex items-center justify-between outline-none hover:bg-slate-50 dark:hover:bg-gray-800/50 rounded px-1 transition-colors">
                        <div class="flex items-center">
                             <span class="mr-2 px-2 py-0.5 bg-indigo-50 dark:bg-blue-900/30 rounded text-indigo-700 dark:text-blue-300">{{sIndex + 1}}</span> {{ section.section }}
                        </div>
                        <i class="fas fa-chevron-down transition-transform duration-200" :class="{'rotate-180': activeSections.includes(sIndex)}"></i>
                    </button>


                    <div class="space-y-4 mb-6" v-if="section && section.fields" v-show="activeSections.includes(sIndex)">
                    <div v-for="(field, fIndex) in section.fields" :key="fIndex" v-show="field.type !== 'hidden'">
                        <label v-if="field.type !== 'checkbox'" class="block text-gray-600 dark:text-gray-300 text-xs mb-1 font-medium">{{ field.label }}</label>
                        
                        <!-- Text/Textarea/Number -->
                        <textarea v-if="['text', 'textarea'].includes(field.type)" v-model="formData[field.variable]" rows="2"
                            :readonly="['nama_penandatangan', 'jabatan_penandatangan', 'nip_penandatangan'].includes(field.variable)"
                            :class="{'bg-gray-100 dark:bg-gray-700 cursor-not-allowed text-gray-500': ['nama_penandatangan', 'jabatan_penandatangan', 'nip_penandatangan'].includes(field.variable)}"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm"></textarea>

                        <input v-if="field.type === 'number'" type="number" v-model="formData[field.variable]"
                             :readonly="['nama_penandatangan', 'jabatan_penandatangan', 'nip_penandatangan'].includes(field.variable)"
                             :class="{'bg-gray-100 dark:bg-gray-700 cursor-not-allowed text-gray-500': ['nama_penandatangan', 'jabatan_penandatangan', 'nip_penandatangan'].includes(field.variable)}"
                             class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                        
                        <input v-if="field.type === 'date'" type="date" v-model="formData[field.variable]"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                            
                        <!-- Select -->
                        <select v-if="field.type === 'select'" v-model="formData[field.variable]"
                            class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                            <option v-for="opt in field.options" :value="opt">{{ opt }}</option>
                        </select>

                        <!-- Auto-Selected Pejabat (Read-only) -->
                        <div v-if="field.type === 'select_pejabat'">
                            <input type="text" :value="formData.nama_penandatangan ? formData.nama_penandatangan + ' (' + (formData.jabatan_penandatangan_select || formData.jabatan_penandatangan) + ')' : 'Memuat pejabat default...'" readonly 
                                class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded px-3 py-2 text-sm cursor-not-allowed focus:outline-none">
                            <p class="text-[10px] text-gray-400 mt-1 italic">*Otomatis diambil dari Master Pejabat (Default)</p>
                        </div>

                        <!-- Select Jabatan (Master - Readonly) -->
                        <div v-if="field.type === 'select_jabatan'">
                            <input type="text" v-model="formData[field.variable]" readonly 
                                class="w-full bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 rounded px-3 py-2 text-sm cursor-not-allowed focus:outline-none">
                            <p class="text-[10px] text-gray-400 mt-1 italic">*Diatur dari Master Pejabat (Default)</p>
                        </div>

                        <!-- Image Upload (New) -->
                        <div v-if="field.type === 'image'">
                            <input type="file" @change="handleGenericImageUpload($event, field.variable, field.width_variable, field.default_width)" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 dark:text-gray-400 dark:file:bg-amber-900 dark:file:text-amber-200">
                            
                            <div v-if="formData[field.variable]" class="mt-2 bg-white dark:bg-gray-800 p-2 rounded border border-gray-200 dark:border-gray-600">
                                <div class="flex justify-center mb-2">
                                    <img :src="formData[field.variable]" class="object-contain" :style="{width: (formData[field.width_variable] || field.default_width || 100) + 'px'}">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <label class="text-xs text-gray-500">Width:</label>
                                    <input type="range" v-model="formData[field.width_variable]" min="20" max="300" class="w-full h-1 bg-gray-300 rounded-lg appearance-none cursor-pointer">
                                    <input type="number" v-model="formData[field.width_variable]" class="w-12 text-xs text-center border rounded dark:bg-gray-700 dark:text-white">
                                </div>
                                <div class="text-center mt-1">
                                    <button @click="formData[field.variable] = null" class="text-xs text-red-500 hover:underline">Remove</button>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox -->
                        <div v-if="field.type === 'checkbox'" class="flex items-center mt-2">
                            <input type="checkbox" v-model="formData[field.variable]" :id="'cb-'+fIndex+'-'+sIndex" 
                                class="w-4 h-4 text-indigo-600 bg-white border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            <label :for="'cb-'+fIndex+'-'+sIndex" class="ml-2 text-sm font-medium text-slate-700 dark:text-gray-300">{{ field.label }}</label>
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
            <button @click="saveDraft" :disabled="isSaving" class="bg-teal-600 hover:bg-teal-700 disabled:opacity-50 disabled:cursor-not-allowed text-white py-2 rounded shadow-md font-semibold transition flex items-center justify-center">
                <i class="fas" :class="isSaving ? 'fa-spinner fa-spin' : 'fa-save'"></i> <span class="ml-2">{{ isSaving ? 'Saving...' : 'Save' }}</span>
            </button>
            <button @click="printPdf" class="bg-slate-700 hover:bg-slate-800 text-white py-2 rounded shadow-md font-semibold transition flex items-center justify-center">
                <i class="fas fa-print mr-2"></i> Print
            </button>
            <button @click="exportWord" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 py-2 rounded shadow-sm font-semibold transition flex items-center justify-center">
                <i class="fas fa-file-word mr-2 text-blue-600"></i> Word
            </button>
            <button @click="exportPdf" class="bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 py-2 rounded shadow-sm font-semibold transition flex items-center justify-center">
                <i class="fas fa-file-pdf mr-2 text-red-600"></i> PDF
            </button>
            <!-- Developer Footer -->
            <div class="col-span-2 text-center pt-2">
                <p class="text-[10px] text-gray-400 dark:text-gray-500 font-medium">
                    Developed by Rahmat Triadi, S.Kom. &copy; <?php echo date('Y'); ?>
                </p>
            </div>
            <!-- Hidden button for ID storage hack -->
            <div id="btn-print-hidden" data-id="" style="display:none;"></div>
        </div>
    </div>

    <!-- Main Content (Preview) -->
    <div class="flex-1 bg-slate-200/50 dark:bg-gray-900 overflow-auto p-8 relative transition-colors duration-200">
        
        <!-- Zoom Controls (Floating) -->
        <div class="fixed bottom-20 right-8 z-40 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 flex flex-col p-1.5 space-y-1 no-print">
            <button @click="zoomIn" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition" title="Zoom In">
                <i class="fas fa-plus"></i>
            </button>
            <div class="text-[10px] font-bold text-center text-gray-500 py-1 cursor-default">{{ Math.round(zoomScale * 100) }}%</div>
            <button @click="zoomOut" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition" title="Zoom Out">
                <i class="fas fa-minus"></i>
            </button>
             <button @click="resetZoom" class="w-8 h-8 flex items-center justify-center rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition border-t border-gray-100 dark:border-gray-700 mt-1" title="Reset Zoom">
                <i class="fas fa-expand"></i>
            </button>
        </div>

        <!-- Canvas -->
        <!-- 1. Hidden Source (Raw HTML) -->
        <div id="raw-content" style="position:absolute; left:-9999px; top:0; width: 210mm; visibility: hidden;">
             <div class="paper-preview" v-html="previewHtml"></div>
        </div>

        <!-- 2. Visible Pagination Container -->
        <div id="pagination-container" class="flex flex-col items-center space-y-8 pb-10 transition-transform duration-200 origin-top font-bookman" :style="{ transform: 'scale(' + zoomScale + ')' }">
            <!-- Pages will be injected here by JS -->
        </div>
    </div>

    <style>
        .font-bookman {
            font-family: 'Bookman Old Style', serif;
        }
        /* Ensure pages always use the font */
        .paper-page {
            font-family: 'Bookman Old Style', serif !important;
        }
        @media print {
            body {
                background-color: white !important;
                -webkit-print-color-adjust: exact;
            }
            .paper-page, div.paper-page {
                box-shadow: none !important;
                margin: 0 !important;
                border: none !important;
                width: 100% !important;
                page-break-after: always;
            }
            /* Hide controls & sidebar */
            .no-print, aside, nav, .zoom-controls { display: none !important; }
            
            /* Hide the grey background wrapper */
            .bg-slate-200\/50 {
                background-color: white !important;
            }
            #raw-content { display: none !important; }
        }
    </style>

</div>

<!-- Vue Application Logic -->
<script src="<?php echo base_url('assets/js/mustache.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/sk_editor_vue.js'); ?>"></script>
</body>
</html>
