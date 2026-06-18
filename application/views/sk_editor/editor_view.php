<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart SK Editor - <?php echo $template->nama_sk; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                        }
                    },
                    fontFamily: {
                        'bookman': ['"Bookman Old Style"', 'Georgia', 'serif'],
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
    <!-- Vue 3 -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- TinyMCE -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        <?php
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
        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }

        /* Custom Fonts */
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('<?php echo base_url('assets/BOOKOS.TTF'); ?>') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Bookman Old Style';
            src: url('<?php echo base_url('assets/BOOKOSB.TTF'); ?>') format('truetype');
            font-weight: bold;
        }

        /* Paper Preview */
        .paper-preview {
            background: white;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.15), 0 0 0 1px rgba(0,0,0,0.05);
            min-height: 297mm;
            margin: 0 auto;
            font-family: 'Bookman Old Style', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: black;
        }

        .paper-preview table { width: 100%; border-collapse: collapse; }
        .paper-preview td, .paper-preview th { word-wrap: break-word; }
        
        .attachment-content table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
        .attachment-content td, .attachment-content th { border: 1px solid #000; padding: 4px; vertical-align: top; }

        .paper-preview ul, .paper-preview ol { margin: 0 0 0.5em 0; padding-left: 2em; list-style-position: outside; }
        .paper-preview li { display: list-item !important; margin-bottom: 0.25em; }
        .paper-preview ul { list-style-type: disc !important; }
        .paper-preview ol { list-style-type: decimal !important; }

        [v-cloak] { display: none; }

        /* Floating Panel */
        .floating-panel {
            backdrop-filter: blur(12px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        .dark .floating-panel {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Input Focus Ring */
        .input-focus {
            transition: all 0.2s ease;
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.15);
        }

        /* Smooth Accordion */
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .accordion-content.open {
            max-height: 2000px;
        }

        /* Print Styles */
        @media print {
            @page { margin: 0; }
            
            *, *::before, *::after {
                box-sizing: border-box;
            }
            
            html, body { 
                background: white !important; 
                margin: 0 !important; 
                padding: 0 !important;
                height: auto !important;
                width: auto !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            #app { 
                display: block !important; 
                height: auto !important;
                width: 100% !important;
                overflow: visible !important;
                flex: none !important;
            }
            
            #app > aside, .no-print, .floating-panel, header, .fixed { 
                display: none !important; 
            }
            
            #raw-content {
                display: none !important;
            }
            
            main {
                display: block !important;
                height: auto !important;
                width: 100% !important;
                overflow: visible !important;
                flex: none !important;
            }
            
            main > div,
            .flex-1.overflow-auto {
                display: block !important;
                overflow: visible !important;
                padding: 0 !important;
                height: auto !important;
                flex: none !important;
            }
            
            #pagination-container {
                display: block !important;
                transform: none !important;
                padding: 0 !important;
                margin: 0 !important;
                gap: 0 !important;
                flex-direction: column !important;
            }
            
            #pagination-container > * {
                margin-top: 0 !important;
                margin-bottom: 0 !important;
            }
            
            .paper-page {
                box-shadow: none !important;
                margin: 0 !important;
                page-break-after: always !important;
                page-break-inside: avoid !important;
                break-after: page !important;
                break-inside: avoid !important;
                overflow: visible !important;
                display: block !important;
                position: relative !important;
            }
            
            .paper-page:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
            
            .page-content {
                overflow: visible !important;
                height: auto !important;
            }
            
            .paper-preview { 
                box-shadow: none !important; 
                margin: 0 !important; 
            }
            
            /* Reset Tailwind spacing utilities for print */
            .space-y-6 > :not([hidden]) ~ :not([hidden]) {
                margin-top: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 min-h-screen font-sans antialiased">

<div id="app" v-cloak class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
           class="fixed lg:static inset-y-0 left-0 z-50 w-[380px] bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transform transition-transform duration-300 ease-out">
        
        <!-- Sidebar Header -->
        <div class="h-16 px-5 flex items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-gradient-to-r from-primary-600 to-primary-500">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                    <i class="fas fa-file-signature text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-base">Smart SK Editor</h1>
                    <p class="text-primary-100 text-[11px] truncate max-w-[180px]"><?php echo $template->nama_sk; ?></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="toggleTheme" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                    <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>
                <a href="<?php echo site_url('sk_editor'); ?>" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" title="Kembali">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>
        </div>

        <!-- Sidebar Tabs -->
        <div class="flex border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <button @click="activeTab = 'form'" 
                    :class="activeTab === 'form' ? 'text-primary-600 border-primary-500 bg-white dark:bg-slate-800' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300'"
                    class="flex-1 px-4 py-3 text-sm font-semibold border-b-2 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-edit"></i> Isi Data
            </button>
            <button @click="activeTab = 'settings'" 
                    :class="activeTab === 'settings' ? 'text-primary-600 border-primary-500 bg-white dark:bg-slate-800' : 'text-slate-500 border-transparent hover:text-slate-700 dark:hover:text-slate-300'"
                    class="flex-1 px-4 py-3 text-sm font-semibold border-b-2 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-cog"></i> Pengaturan
            </button>
        </div>

        <!-- Sidebar Content -->
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            
            <!-- Form Tab -->
            <div v-show="activeTab === 'form'" class="p-4 space-y-3">
                
                <!-- Dynamic Data Section (From Template Variables) -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700">
                    <button @click="toggleDataSection" 
                            class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-primary-500 text-white text-xs font-bold flex items-center justify-center">
                                <i class="fas fa-file-alt"></i>
                            </span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200 text-sm">Data SK</span>
                            <span class="text-xs text-slate-400">({{ templateVariables.length }} field)</span>
                        </div>
                        <i class="fas fa-chevron-down text-slate-400 transition-transform duration-200" :class="{'rotate-180': isDataSectionOpen}"></i>
                    </button>
                    
                    <div v-show="isDataSectionOpen" class="px-4 pb-4 space-y-4 border-t border-slate-200 dark:border-slate-700 pt-4">
                        <!-- Empty State -->
                        <div v-if="templateVariables.length === 0" class="text-center py-6 text-slate-400">
                            <i class="fas fa-inbox text-3xl mb-2"></i>
                            <p class="text-sm">Tidak ada variabel dalam template ini</p>
                        </div>
                        
                        <!-- Dynamic Fields -->
                        <div v-for="(field, index) in templateVariables" :key="field.variable">
                            
                            <!-- Label -->
                            <label v-if="field.type !== 'checkbox'" class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                {{ field.label }}
                            </label>
                            
                            <!-- Text/Textarea -->
                            <textarea v-if="['text', 'textarea'].includes(field.type)" 
                                      v-model="formData[field.variable]" 
                                      rows="2"
                                      :placeholder="'Masukkan ' + field.label"
                                      class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition input-focus resize-none"></textarea>

                            <!-- Number -->
                            <input v-if="field.type === 'number'" 
                                   type="number" 
                                   v-model="formData[field.variable]"
                                   :placeholder="'Masukkan ' + field.label"
                                   class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition input-focus">
                            
                            <!-- Date -->
                            <input v-if="field.type === 'date'" 
                                   type="date" 
                                   v-model="formData[field.variable]"
                                   class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition input-focus">
                                
                            <!-- Checkbox -->
                            <label v-if="field.type === 'checkbox'" class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative">
                                    <input type="checkbox" v-model="formData[field.variable]" class="sr-only peer">
                                    <div class="w-5 h-5 border-2 border-slate-300 dark:border-slate-600 rounded peer-checked:bg-primary-500 peer-checked:border-primary-500 transition-colors"></div>
                                    <i class="fas fa-check absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-white text-[10px] opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                                </div>
                                <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-primary-600 transition-colors">{{ field.label }}</span>
                            </label>

                            <!-- Image Upload -->
                            <div v-if="field.type === 'image'" class="space-y-2">
                                <label class="flex items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg cursor-pointer hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                                    <input type="file" @change="handleGenericImageUpload($event, field.variable, field.variable + '_width', 70)" accept="image/*" class="hidden">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 mb-1"></i>
                                        <p class="text-xs text-slate-500">Klik untuk upload</p>
                                    </div>
                                </label>
                                <div v-if="formData[field.variable]" class="bg-slate-50 dark:bg-slate-800 p-3 rounded-lg border">
                                    <img :src="formData[field.variable]" class="max-h-16 mx-auto object-contain mb-2">
                                    <input type="range" v-model="formData[field.variable + '_width']" min="20" max="300" class="w-full">
                                    <button @click="formData[field.variable] = null" class="text-xs text-red-500 hover:underline mt-1">Hapus</button>
                                </div>
                            </div>

                            <!-- Repeater -->
                            <div v-if="field.type === 'repeater'" class="space-y-2">
                                <div v-for="(item, rIndex) in formData[field.variable]" :key="rIndex" class="flex gap-2 items-start group">
                                    <span class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 text-xs flex items-center justify-center shrink-0 mt-2">{{ rIndex + 1 }}</span>
                                    <textarea v-model="formData[field.variable][rIndex]" rows="2"
                                              class="flex-1 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none resize-none"></textarea>
                                    <button @click="removeRepeaterItem(field.variable, rIndex)" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition opacity-0 group-hover:opacity-100 mt-1">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <button @click="addRepeaterItem(field.variable)" class="w-full py-2 border-2 border-dashed border-slate-300 dark:border-slate-600 text-slate-500 rounded-lg hover:border-primary-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors text-sm font-medium">
                                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Penandatangan Section -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-xl overflow-hidden border border-indigo-200 dark:border-indigo-800">
                    <button @click="isPenandatanganOpen = !isPenandatanganOpen" 
                            class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-indigo-100/50 dark:hover:bg-indigo-800/20 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-indigo-500 text-white text-xs font-bold flex items-center justify-center">
                                <i class="fas fa-user-tie"></i>
                            </span>
                            <span class="font-semibold text-indigo-800 dark:text-indigo-200 text-sm">Penandatangan</span>
                        </div>
                        <i class="fas fa-chevron-down text-indigo-500 transition-transform duration-200" :class="{'rotate-180': isPenandatanganOpen}"></i>
                    </button>
                    
                    <div v-show="isPenandatanganOpen" class="px-4 pb-4 space-y-3 border-t border-indigo-200 dark:border-indigo-700 pt-4">
                        
                        <!-- Pejabat Dropdown -->
                        <div>
                            <label class="block text-xs font-semibold text-indigo-700 dark:text-indigo-300 mb-1.5 uppercase tracking-wide">
                                <i class="fas fa-user-check mr-1"></i> Pilih Pejabat
                            </label>
                            <select @change="setPejabat($event.target.value)" 
                                    class="w-full bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                                <option value="">-- Pilih dari Master Pejabat --</option>
                                <option v-for="p in pejabatList" :key="p.id" :value="p.id" :selected="formData.jabatan_penandatangan_select === p.jabatan">
                                    {{ p.nama }} - {{ p.jabatan }}
                                </option>
                            </select>
                            <p class="text-[10px] text-indigo-400 mt-1"><i class="fas fa-info-circle mr-1"></i>Otomatis terisi, tetap bisa diubah manual</p>
                        </div>

                        <!-- Nama Penandatangan -->
                        <div>
                            <label class="block text-xs font-semibold text-indigo-700 dark:text-indigo-300 mb-1.5 uppercase tracking-wide">
                                Nama Penandatangan
                            </label>
                            <input type="text" 
                                   v-model="formData.nama_penandatangan"
                                   placeholder="Nama lengkap penandatangan"
                                   class="w-full bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>

                        <!-- Jabatan Penandatangan -->
                        <div>
                            <label class="block text-xs font-semibold text-indigo-700 dark:text-indigo-300 mb-1.5 uppercase tracking-wide">
                                Jabatan Penandatangan
                            </label>
                            <input type="text" 
                                   v-model="formData.jabatan_penandatangan"
                                   placeholder="Jabatan penandatangan"
                                   class="w-full bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>

                        <!-- NIP Penandatangan -->
                        <div>
                            <label class="block text-xs font-semibold text-indigo-700 dark:text-indigo-300 mb-1.5 uppercase tracking-wide">
                                NIP Penandatangan
                            </label>
                            <input type="text" 
                                   v-model="formData.nip_penandatangan"
                                   placeholder="NIP penandatangan"
                                   class="w-full bg-white dark:bg-slate-800 border border-indigo-300 dark:border-indigo-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition">
                        </div>
                    </div>
                </div>

                <!-- Mandatory Settings Section (Always Present) -->
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl overflow-hidden border border-amber-200 dark:border-amber-800">
                    <button @click="toggleMandatorySection" 
                            class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-amber-100/50 dark:hover:bg-amber-800/20 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-amber-500 text-white text-xs font-bold flex items-center justify-center">
                                <i class="fas fa-cogs"></i>
                            </span>
                            <span class="font-semibold text-amber-800 dark:text-amber-200 text-sm">Pengaturan Wajib</span>
                        </div>
                        <i class="fas fa-chevron-down text-amber-500 transition-transform duration-200" :class="{'rotate-180': isMandatorySectionOpen}"></i>
                    </button>
                    
                    <div v-show="isMandatorySectionOpen" class="px-4 pb-4 space-y-4 border-t border-amber-200 dark:border-amber-700 pt-4">
                        
                        <!-- Nomor Urut -->
                        <div>
                            <label class="block text-xs font-semibold text-amber-700 dark:text-amber-300 mb-1.5 uppercase tracking-wide">
                                <i class="fas fa-sort-numeric-up mr-1"></i> Nomor Urut SK
                            </label>
                            <input type="number" 
                                   v-model="mandatorySettings.nomor_urut" 
                                   min="1"
                                   class="w-full bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition">
                        </div>

                        <!-- Jumlah Salinan -->
                        <div>
                            <label class="block text-xs font-semibold text-amber-700 dark:text-amber-300 mb-1.5 uppercase tracking-wide">
                                <i class="fas fa-copy mr-1"></i> Jumlah Salinan
                            </label>
                            <input type="number" 
                                   v-model="mandatorySettings.jumlah_salinan" 
                                   min="1" max="100"
                                   class="w-full bg-white dark:bg-slate-800 border border-amber-300 dark:border-amber-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition">
                        </div>

                        <!-- Toggle Switches -->
                        <div class="space-y-3 pt-2">
                            <!-- Tampilkan Hijriah -->
                            <label class="flex items-center justify-between cursor-pointer group p-2 rounded-lg hover:bg-amber-100/50 dark:hover:bg-amber-800/20 transition">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-moon text-amber-600 dark:text-amber-400 w-5 text-center"></i>
                                    <span class="text-sm text-amber-800 dark:text-amber-200 font-medium">Tampilkan Tanggal Hijriah</span>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" v-model="mandatorySettings.tampilkan_hijriah" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-amber-500 transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                                </div>
                            </label>

                            <!-- Tampilkan NIP -->
                            <label class="flex items-center justify-between cursor-pointer group p-2 rounded-lg hover:bg-amber-100/50 dark:hover:bg-amber-800/20 transition">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-id-card text-amber-600 dark:text-amber-400 w-5 text-center"></i>
                                    <span class="text-sm text-amber-800 dark:text-amber-200 font-medium">Tampilkan NIP</span>
                                </div>
                                <div class="relative">
                                    <input type="checkbox" v-model="mandatorySettings.tampilkan_nip" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-amber-500 transition-colors"></div>
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Salinan / Tembusan Section -->
                <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-xl overflow-hidden border border-emerald-200 dark:border-emerald-800">
                    <button @click="isSalinanOpen = !isSalinanOpen" 
                            class="w-full px-4 py-3 flex items-center justify-between text-left hover:bg-emerald-100/50 dark:hover:bg-emerald-800/20 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center">
                                <i class="fas fa-paper-plane"></i>
                            </span>
                            <span class="font-semibold text-emerald-800 dark:text-emerald-200 text-sm">Salinan / Tembusan</span>
                            <span v-if="formData.salinan && formData.salinan.length > 0" class="text-[10px] bg-emerald-500 text-white px-1.5 py-0.5 rounded-full">{{ formData.salinan.length }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-emerald-500 transition-transform duration-200" :class="{'rotate-180': isSalinanOpen}"></i>
                    </button>
                    
                    <div v-show="isSalinanOpen" class="px-4 pb-4 space-y-3 border-t border-emerald-200 dark:border-emerald-700 pt-4">
                        
                        <!-- Jenis Distribusi -->
                        <div class="mb-3">
                            <label class="block text-xs font-medium text-emerald-700 dark:text-emerald-400 mb-1.5">Jenis Distribusi</label>
                            <select v-model="globalSettings.distribusi_type" class="w-full bg-emerald-50/50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-700 text-emerald-800 dark:text-emerald-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none">
                                <option value="salinan">Salinan Keputusan ini disampaikan kepada:</option>
                                <option value="petikan">Petikan Keputusan ini disampaikan kepada:</option>
                                <option value="tembusan">Tembusan:</option>
                                <option value="tembusan_yth">Tembusan Yth:</option>
                            </select>
                        </div>

                        <!-- Salinan Items -->
                        <div v-for="(item, sIndex) in formData.salinan" :key="sIndex" class="flex gap-2 items-start group">
                            <span class="w-6 h-6 rounded-full bg-emerald-200 dark:bg-emerald-700 text-emerald-700 dark:text-emerald-200 text-xs flex items-center justify-center shrink-0 mt-2">{{ sIndex + 1 }}</span>
                            <input type="text" v-model="formData.salinan[sIndex]" 
                                   placeholder="Nama penerima salinan"
                                   class="flex-1 bg-white dark:bg-slate-800 border border-emerald-300 dark:border-emerald-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none transition">
                            <button @click="removeSalinan(sIndex)" class="w-8 h-8 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition opacity-0 group-hover:opacity-100 mt-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- Add Button -->
                        <button @click="addSalinan" class="w-full py-2 border-2 border-dashed border-emerald-300 dark:border-emerald-600 text-emerald-600 dark:text-emerald-400 rounded-lg hover:border-emerald-400 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors text-sm font-medium">
                            <i class="fas fa-plus mr-1"></i> Tambah Penerima Salinan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div v-show="activeTab === 'settings'" class="p-4 space-y-4">
                
                <!-- Paper Settings -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fas fa-file-alt text-primary-500"></i> Ukuran Kertas
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Ukuran</label>
                            <select v-model="globalSettings.paperSize" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none">
                                <option value="A4">A4</option>
                                <option value="F4">F4 / Folio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Orientasi</label>
                            <select v-model="globalSettings.orientation" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Margins -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fas fa-expand-arrows-alt text-primary-500"></i> Margin (mm)
                    </h3>
                    <div class="grid grid-cols-4 gap-2">
                        <div class="text-center">
                            <label class="block text-[10px] text-slate-400 dark:text-slate-500 mb-1">Atas</label>
                            <input type="number" v-model="globalSettings.marginTop" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-2 py-2 text-sm text-center focus:border-primary-500 outline-none">
                        </div>
                        <div class="text-center">
                            <label class="block text-[10px] text-slate-400 dark:text-slate-500 mb-1">Bawah</label>
                            <input type="number" v-model="globalSettings.marginBottom" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-2 py-2 text-sm text-center focus:border-primary-500 outline-none">
                        </div>
                        <div class="text-center">
                            <label class="block text-[10px] text-slate-400 dark:text-slate-500 mb-1">Kiri</label>
                            <input type="number" v-model="globalSettings.marginLeft" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-2 py-2 text-sm text-center focus:border-primary-500 outline-none">
                        </div>
                        <div class="text-center">
                            <label class="block text-[10px] text-slate-400 dark:text-slate-500 mb-1">Kanan</label>
                            <input type="number" v-model="globalSettings.marginRight" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-2 py-2 text-sm text-center focus:border-primary-500 outline-none">
                        </div>
                    </div>
                </div>

                <!-- Pengaturan TTD -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fas fa-signature text-primary-500"></i> Pengaturan TTD
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Master Pengadilan</label>
                            <input type="text" v-model="globalSettings.master_pengadilan" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none" placeholder="PENGADILAN AGAMA GORONTALO">
                            <p class="text-[10px] text-slate-400 mt-1">Digabung dengan jabatan pada Diktum (Contoh: KETUA PENGADILAN AGAMA GORONTALO)</p>
                        </div>
                        <label class="flex items-center justify-between cursor-pointer group pt-2 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">Tampilkan Gelar Pejabat</span>
                            </div>
                            <div class="relative">
                                <input type="checkbox" v-model="globalSettings.show_gelar" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-primary-500 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Typography -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fas fa-text-height text-primary-500"></i> Tipografi
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Ukuran Font</label>
                            <select v-model="globalSettings.fontSize" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none">
                                <option value="10pt">10pt</option>
                                <option value="11pt">11pt</option>
                                <option value="12pt">12pt</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Spasi Baris</label>
                            <select v-model="globalSettings.lineHeight" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none">
                                <option value="1.0">1.0</option>
                                <option value="1.15">1.15</option>
                                <option value="1.5">1.5</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Pengaturan Diktum -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-ol text-primary-500"></i> Pengaturan Diktum
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Gaya Penomoran -->
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Gaya Penomoran Diktum</label>
                            <select v-model="globalSettings.diktum_style" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none">
                                <option value="baku">KESATU, KEDUA, KETIGA...</option>
                                <option value="alternatif">PERTAMA, KEDUA, KETIGA...</option>
                                <option value="angka">Angka (1., 2., 3., ...)</option>
                            </select>
                        </div>

                        <!-- Header Diktum -->
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1.5">Awalan Header Diktum</label>
                            <select v-model="globalSettings.diktum_header_type" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 text-sm focus:border-primary-500 outline-none">
                                <option value="keputusan">KEPUTUSAN</option>
                                <option value="keputusan_bersama">KEPUTUSAN BERSAMA</option>
                                <option value="penetapan">PENETAPAN</option>
                            </select>
                        </div>
                        
                        <!-- Diktum Justify Toggle -->
                        <label class="flex items-center justify-between cursor-pointer pt-2 border-t border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200 block text-sm">Diktum Rata Kiri-Kanan</span>
                                    <span class="text-[10px] text-slate-400">Terapkan rata kiri-kanan pada isi diktum.</span>
                                </div>
                            </div>
                            <div class="relative">
                                <input type="checkbox" v-model="globalSettings.diktum_justify" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-primary-500 transition-colors"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Page Numbers Toggle -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-list-ol text-primary-500"></i>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Nomor Halaman</span>
                        </div>
                        <div class="relative">
                            <input type="checkbox" v-model="globalSettings.showPageNumbers" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 dark:bg-slate-600 rounded-full peer-checked:bg-primary-500 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                        </div>
                    </label>
                </div>

                <!-- Logo Tengah -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <i class="fas fa-image text-primary-500"></i> Logo Tengah SK
                    </h3>
                    <label class="flex items-center justify-center w-full h-24 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg cursor-pointer hover:border-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                        <input type="file" @change="handleContentLogoUpload" accept="image/*" class="hidden">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-2xl text-slate-400 mb-1"></i>
                            <p class="text-xs text-slate-500">Upload Logo</p>
                        </div>
                    </label>
                    <div v-if="formData.logo_tengah || formData.skContentLogo" class="mt-3 p-3 bg-white dark:bg-slate-800 rounded-lg border">
                        <img :src="formData.logo_tengah || formData.skContentLogo" class="max-h-16 mx-auto object-contain mb-2">
                        <div class="flex items-center gap-2">
                            <input type="range" v-model="formData.logo_tengah_width" min="40" max="300" class="flex-1">
                            <span class="text-xs text-slate-500 w-12 text-right">{{ formData.logo_tengah_width || 100 }}px</span>
                        </div>
                        <button @click="formData.logo_tengah = null; formData.skContentLogo = null" class="text-xs text-red-500 hover:underline mt-2 block mx-auto">Hapus Logo</button>
                    </div>
                </div>

                <!-- Attachments -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <i class="fas fa-paperclip text-primary-500"></i> Lampiran
                        </h3>
                        <button @click="addAttachment" class="px-3 py-1.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-xs font-medium transition">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </button>
                    </div>
                    <div v-if="formData.attachments && formData.attachments.length > 0" class="space-y-3">
                        <div v-for="(att, index) in formData.attachments" :key="index" class="bg-white dark:bg-slate-800 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-primary-500 uppercase">Lampiran {{ index + 1 }}</span>
                                <button @click="removeAttachment(index)" class="text-red-400 hover:text-red-600 text-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <input type="text" v-model="att.title" class="w-full mb-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2 text-sm text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500" placeholder="Judul Lampiran">
                            <textarea :id="'attachment-editor-' + index" class="tinymce-editor w-full h-32"></textarea>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-slate-400">
                        <i class="fas fa-inbox text-3xl mb-2"></i>
                        <p class="text-sm">Belum ada lampiran</p>
                    </div>
                </div>

                <!-- Save Defaults -->
                <button @click="saveAsDefault" class="w-full py-3 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i> Simpan Sebagai Default
                </button>
            </div>
        </div>

        <!-- Sidebar Footer Actions -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
            <div class="grid grid-cols-2 gap-2 mb-2">
                <button @click="saveDraft" :disabled="isSaving" 
                        class="bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2 shadow-lg shadow-primary-500/25">
                    <i class="fas" :class="isSaving ? 'fa-spinner fa-spin' : 'fa-save'"></i>
                    {{ isSaving ? 'Menyimpan...' : 'Simpan' }}
                </button>
                <button @click="printPdf" class="bg-slate-700 hover:bg-slate-800 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <button @click="exportWord" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fas fa-file-word text-blue-500"></i> Word
                </button>
                <button @click="exportPdf" class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf text-red-500"></i> PDF
                </button>
            </div>
            <p class="text-center text-[10px] text-slate-400 mt-3">
                Smart SK Editor v2.1 &copy; <?php echo date('Y'); ?>
            </p>
        </div>
    </aside>

    <!-- Mobile Overlay -->
    <div v-show="isSidebarOpen" @click="toggleSidebar" class="fixed inset-0 bg-black/50 z-40 lg:hidden backdrop-blur-sm"></div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 bg-slate-200/50 dark:bg-slate-950">
        
        <!-- Top Bar -->
        <header class="h-14 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-6 shrink-0">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar" class="lg:hidden w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h2 class="font-bold text-slate-800 dark:text-white text-sm lg:text-base"><?php echo $template->nama_sk; ?></h2>
                    <p class="text-xs text-slate-500">Template ID: <?php echo $template->id; ?></p>
                </div>
            </div>
            
            <!-- Zoom Controls -->
            <div class="flex items-center gap-1 bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
                <button @click="zoomOut" class="w-8 h-8 rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" title="Zoom Out">
                    <i class="fas fa-minus text-xs"></i>
                </button>
                <span class="w-14 text-center text-xs font-semibold text-slate-600 dark:text-slate-300">{{ Math.round(zoomScale * 100) }}%</span>
                <button @click="zoomIn" class="w-8 h-8 rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" title="Zoom In">
                    <i class="fas fa-plus text-xs"></i>
                </button>
                <div class="w-px h-5 bg-slate-300 dark:bg-slate-600 mx-1"></div>
                <button @click="resetZoom" class="w-8 h-8 rounded-md hover:bg-white dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition" title="Reset Zoom">
                    <i class="fas fa-expand text-xs"></i>
                </button>
            </div>
        </header>

        <!-- Preview Area -->
        <div class="flex-1 overflow-auto p-4 lg:p-8">
            <!-- Hidden Source -->
            <div id="raw-content" style="position:absolute; left:-9999px; top:0; width: 210mm; visibility: hidden;">
                <div class="paper-preview" v-html="previewHtml"></div>
            </div>

            <!-- Visible Pagination Container -->
            <div id="pagination-container" 
                 class="flex flex-col items-center space-y-6 pb-10 transition-transform duration-200 origin-top" 
                 :style="{ transform: 'scale(' + zoomScale + ')' }">
                <!-- Pages injected by JS -->
            </div>
        </div>
    </main>

    <!-- Floating Quick Actions (Mobile) -->
    <div class="fixed bottom-6 right-6 z-30 lg:hidden flex flex-col gap-2 no-print">
        <button @click="saveDraft" :disabled="isSaving" 
                class="w-14 h-14 rounded-full bg-primary-600 hover:bg-primary-700 text-white shadow-lg shadow-primary-500/30 flex items-center justify-center">
            <i class="fas" :class="isSaving ? 'fa-spinner fa-spin' : 'fa-save'" class="text-xl"></i>
        </button>
    </div>
</div>

<!-- Vue Application -->
<script src="<?php echo base_url('assets/js/mustache.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/sk_editor_vue.js?v=' . (time()+1)); ?>"></script>
</body>
</html>
