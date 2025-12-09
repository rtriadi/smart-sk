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

        <!-- Kop Surat Settings -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-200">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-gray-700 pb-2">
                <h3 class="text-lg font-bold text-emerald-600 dark:text-green-400 uppercase tracking-wider">Kop Surat Defaults</h3>
                <label class="flex items-center cursor-pointer">
                    <span class="mr-2 text-sm text-slate-600 dark:text-gray-400 font-medium">Show by default</span>
                    <input type="checkbox" v-model="settings.showKop" class="w-5 h-5 text-indigo-600 bg-white border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                </label>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block text-slate-700 dark:text-gray-400 text-sm font-semibold mb-1">Logo (Base64/URL)</label>
                    <div class="flex gap-2">
                        <input type="text" v-model="settings.kopLogo" class="flex-1 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2 text-xs shadow-sm focus:border-indigo-500 focus:outline-none">
                        <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden" id="logo-upload">
                        <label for="logo-upload" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-slate-600 dark:text-white px-3 py-2 rounded cursor-pointer text-xs flex items-center border border-gray-200 dark:border-gray-600 transition shadow-sm">
                            <i class="fas fa-upload"></i>
                        </label>
                    </div>
                    <div v-if="settings.kopLogo" class="mt-2 bg-gray-50 border border-gray-200 p-2 rounded inline-block">
                        <img :src="settings.kopLogo" class="h-12 object-contain">
                    </div>
                </div>
                
                <input type="text" v-model="settings.kopTitle1" placeholder="Line 1 (e.g. MAHKAMAH AGUNG RI)" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none">
                <input type="text" v-model="settings.kopTitle2" placeholder="Line 2 (e.g. DIREKTORAT JENDERAL...)" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none">
                <input type="text" v-model="settings.kopTitle3" placeholder="Line 3 (e.g. PENGADILAN TINGGI AGAMA...)" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none">
                <input type="text" v-model="settings.kopTitle4" placeholder="Line 4 (e.g. PENGADILAN AGAMA GORONTALO)" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none">
                <textarea v-model="settings.kopAddress" rows="2" placeholder="Address" class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 text-slate-900 dark:text-white rounded px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none"></textarea>
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
            showKop: true,
            kopLogo: '', // Base64 string
            kopTitle1: '',
            kopTitle2: '',
            kopTitle3: '',
            kopTitle4: '',
            kopAddress: ''
        };

        const settings = ref({ ...defaultSettings });

        // Theme Logic
        const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');

        onMounted(() => {
            // Load Global Settings
            const stored = localStorage.getItem('sk_editor_global_settings');
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
            localStorage.setItem('sk_editor_global_settings', JSON.stringify(settings.value));
            alert('Settings Saved! These will be applied to all new and existing drafts.');
        };

        const handleLogoUpload = (event) => {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                settings.value.kopLogo = e.target.result;
            };
            reader.readAsDataURL(file);
        };

        const dashboardUrl = () => SITE_URL + 'sk_editor'; // Use 'sk_editor' controller index as dashboard

        return {
            settings,
            saveSettings,
            handleLogoUpload,
            dashboardUrl,
            isDarkMode,
            toggleTheme
        };
    }
}).mount('#app');
</script>
</body>
</html>
