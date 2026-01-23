<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Settings</title>
    
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
    <!-- Vue 3 (CDN) -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script>
        var SITE_URL = '<?php echo site_url(); ?>';
    </script>
    <style>
        [v-cloak] { display: none; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-slate-900 dark:text-gray-100 font-sans transition-colors duration-200">

<div id="app" v-cloak class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-200 dark:border-gray-700 pb-4 transition-colors duration-200">
        <div class="flex items-center">
            <a :href="dashboardUrl()" class="text-gray-400 hover:text-indigo-600 dark:hover:text-white mr-4 transition" title="Back to Dashboard">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white transition-colors duration-200">Global Settings</h1>
        </div>
        <div class="flex items-center space-x-3">
             <!-- Theme Toggle -->
            <button @click="toggleTheme" class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-yellow-400 shadow-sm transition flex items-center justify-center mr-2" title="Toggle Theme">
                <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>

            <button @click="saveSettings" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow-md font-semibold transition flex items-center">
                <i class="fas fa-save mr-2"></i> Save Settings
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Paper Settings -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <h3 class="text-lg font-bold text-indigo-600 dark:text-blue-400 mb-4 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">Paper & Layout</h3>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-slate-700 dark:text-gray-400 text-sm font-semibold mb-2">Paper Size</label>
                    <select v-model="settings.paperSize" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2.5 focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                        <option value="A4">A4</option>
                        <option value="F4">F4 (Folio)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 dark:text-gray-400 text-sm font-semibold mb-2">Orientation</label>
                    <select v-model="settings.orientation" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2.5 focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                        <option value="portrait">Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-slate-700 dark:text-gray-400 text-sm font-semibold mb-2">Margins (mm)</label>
                <div class="grid grid-cols-4 gap-2">
                    <div>
                        <label class="text-xs text-gray-500 block mb-1 font-medium">Top</label>
                        <input type="number" v-model="settings.marginTop" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-2 py-2 text-center focus:border-indigo-500 dark:focus:border-blue-500 shadow-sm focus:outline-none transition">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1 font-medium">Bottom</label>
                        <input type="number" v-model="settings.marginBottom" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-2 py-2 text-center focus:border-indigo-500 dark:focus:border-blue-500 shadow-sm focus:outline-none transition">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1 font-medium">Left</label>
                        <input type="number" v-model="settings.marginLeft" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-2 py-2 text-center focus:border-indigo-500 dark:focus:border-blue-500 shadow-sm focus:outline-none transition">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 block mb-1 font-medium">Right</label>
                        <input type="number" v-model="settings.marginRight" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-2 py-2 text-center focus:border-indigo-500 dark:focus:border-blue-500 shadow-sm focus:outline-none transition">
                    </div>
                </div>
            </div>
        </div>

            <!-- Typography Settings -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                <h3 class="text-lg font-bold text-indigo-600 dark:text-blue-400 mb-4 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-2">Typography & Extras</h3>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-slate-700 dark:text-gray-400 text-sm font-semibold mb-2">Font Size</label>
                        <select v-model="settings.fontSize" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2.5 focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                            <option value="10pt">10pt</option>
                            <option value="11pt">11pt</option>
                            <option value="12pt">12pt</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 dark:text-gray-400 text-sm font-semibold mb-2">Line Spacing</label>
                        <select v-model="settings.lineHeight" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2.5 focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-1 focus:ring-indigo-500 outline-none transition shadow-sm">
                            <option value="1.0">1.0</option>
                            <option value="1.15">1.15</option>
                            <option value="1.5">1.5</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between mb-3 border-t border-gray-100 dark:border-gray-700 pt-4">
                    <span class="text-slate-600 dark:text-gray-400 text-sm font-medium">Show Page Numbers</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" v-model="settings.showPageNumbers" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>


    
        </div>
    
    </div>
    
    <script>
    const { createApp, ref, onMounted, watch } = Vue;
    
    createApp({
        setup() {
        const defaultSettings = {
                paperSize: 'A4',
                orientation: 'portrait',
                marginTop: 20,
                marginBottom: 20,
                marginLeft: 20,
                marginRight: 20,
                fontSize: '12pt',
                lineHeight: '1.5',
                showPageNumbers: false
            };
    
            const settings = ref({ ...defaultSettings });
    
            // Theme Logic
            const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');
    
            onMounted(() => {
                // Load Global Settings
                const stored = localStorage.getItem('sk_editor_settings');
                if (stored) {
                    try {
                        const parsed = JSON.parse(stored);
                        settings.value = { ...defaultSettings, ...parsed };
                    } catch (e) {
                        console.error('Failed to load settings', e);
                    }
                }
    
                // Apply Theme
                if (isDarkMode.value) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            });
    
            const toggleTheme = () => {
                isDarkMode.value = !isDarkMode.value;
                if (isDarkMode.value) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('sk_editor_theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('sk_editor_theme', 'light');
                }
            };
    
            const saveSettings = () => {
                localStorage.setItem('sk_editor_settings', JSON.stringify(settings.value));
                alert('Settings Saved! These will be applied to all new and existing drafts.');
            };
    
            const dashboardUrl = () => SITE_URL + 'sk_editor'; // Use 'sk_editor' controller index as dashboard
    
            return {
                settings,
                saveSettings,
                dashboardUrl,
                isDarkMode,
                toggleTheme
            };
        }
    }).mount('#app');
    </script>
    </body>
    </html>
