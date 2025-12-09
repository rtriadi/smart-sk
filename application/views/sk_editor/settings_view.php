<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Smart SK Editor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: { 50:'#f8fafc', 100:'#f1f5f9', 200:'#e2e8f0', 800:'#1e293b', 900:'#0f172a' }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <style>[v-cloak] { display: none; }</style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-slate-900 dark:text-gray-100 font-sans transition-colors duration-200">

<div id="app" v-cloak class="container mx-auto px-4 py-8 max-w-5xl">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-200 dark:border-gray-700 pb-4">
        <div class="flex items-center">
            <a href="<?php echo site_url('sk_editor'); ?>" class="text-gray-500 hover:text-indigo-600 dark:hover:text-white mr-4 transition flex items-center font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Templates
            </a>
            <h1 class="text-2xl font-bold">Settings</h1>
        </div>
        <div class="flex items-center space-x-2">
            <button @click="toggleTheme" class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 shadow-sm flex items-center justify-center">
                <i class="fas" :class="isDarkMode ? 'fa-sun text-yellow-400' : 'fa-moon text-gray-400'"></i>
            </button>
            <a href="<?php echo site_url('auth/logout'); ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow-md transition flex items-center font-medium" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-8">
        <!-- Sidebar Tabs -->
        <div class="col-span-12 md:col-span-3">
            <nav class="space-y-1">
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border-l-4 border-indigo-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'" class="w-full text-left px-4 py-3 font-medium transition flex items-center">
                    <i class="fas fa-sliders-h w-6"></i> General
                </button>
                <button @click="activeTab = 'pejabat'" :class="activeTab === 'pejabat' ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border-l-4 border-indigo-500' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800'" class="w-full text-left px-4 py-3 font-medium transition flex items-center">
                    <i class="fas fa-user-tie w-6"></i> Master Pejabat
                </button>
            </nav>
        </div>

        <!-- Content -->
        <div class="col-span-12 md:col-span-9">
            
            <!-- General Tab -->
            <div v-if="activeTab === 'general'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold mb-6 text-slate-800 dark:text-white">General Settings (Default)</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Paper & Margins -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Paper</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Paper Size</label>
                            <select v-model="generalSettings.paperSize" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="A4">A4</option>
                                <option value="F4">F4</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Orientation</label>
                            <select v-model="generalSettings.orientation" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>
                        
                        <div class="pt-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Margins (mm)</label>
                            <div class="grid grid-cols-4 gap-2">
                                <div><label class="text-xs text-center block text-gray-500">Top</label><input type="number" v-model="generalSettings.marginTop" class="w-full text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-2 text-sm"></div>
                                <div><label class="text-xs text-center block text-gray-500">Btm</label><input type="number" v-model="generalSettings.marginBottom" class="w-full text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-2 text-sm"></div>
                                <div><label class="text-xs text-center block text-gray-500">Left</label><input type="number" v-model="generalSettings.marginLeft" class="w-full text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-2 text-sm"></div>
                                <div><label class="text-xs text-center block text-gray-500">Right</label><input type="number" v-model="generalSettings.marginRight" class="w-full text-center rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-2 text-sm"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kop Surat -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2 flex justify-between items-center">
                            Kop Surat
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="generalSettings.showKop" class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </h3>
                        
                        <div v-if="generalSettings.showKop" class="space-y-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Header Text</label>
                                <input type="text" v-model="generalSettings.kopTitle1" class="w-full mb-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1 text-sm placeholder-gray-400" placeholder="Line 1">
                                <input type="text" v-model="generalSettings.kopTitle2" class="w-full mb-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1 text-sm placeholder-gray-400" placeholder="Line 2">
                                <input type="text" v-model="generalSettings.kopTitle3" class="w-full mb-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1 text-sm placeholder-gray-400" placeholder="Line 3">
                                <input type="text" v-model="generalSettings.kopTitle4" class="w-full mb-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1 text-sm placeholder-gray-400" placeholder="Line 4">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Address</label>
                                <textarea v-model="generalSettings.kopAddress" rows="2" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1 text-sm placeholder-gray-400"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Logo URL / Base64</label>
                                <input type="text" v-model="generalSettings.kopLogo" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-2 py-1 text-xs text-gray-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button @click="saveGeneralSettings" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded shadow-md font-bold transition flex items-center">
                        <i class="fas fa-save mr-2"></i> Save Defaults
                    </button>
                </div>
            </div>

            <!-- Pejabat Tab -->
            <div v-if="activeTab === 'pejabat'" class="space-y-6">
                
                <!-- Add New -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-bold mb-4">{{ isEditing ? 'Edit Pejabat' : 'Add New Pejabat' }}</h2>
                    <form action="<?php echo site_url('sk_editor/save_pejabat'); ?>" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <input type="hidden" name="id" v-model="form.id">
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" v-model="form.nama" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Drs. H. Fulan, M.H.">
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIP</label>
                            <input type="text" name="nip" v-model="form.nip" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. 19900101...">
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jabatan</label>
                            <input type="text" name="jabatan" v-model="form.jabatan" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Ketua">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" v-model="form.status" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="aktif">Aktif</option>
                                <option value="non-aktif">Non-Aktif</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2 flex justify-end space-x-2 mt-2">
                            <button type="button" @click="resetForm" v-if="isEditing" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                {{ isEditing ? 'Update Pejabat' : 'Save Pejabat' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-500 dark:text-gray-400">
                            <tr>
                                <th class="p-4">Nama</th>
                                <th class="p-4">NIP</th>
                                <th class="p-4">Jabatan</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="p in pejabatList" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="p-4 font-medium">{{ p.nama }}</td>
                                <td class="p-4 font-mono text-sm text-gray-500">{{ p.nip || '-' }}</td>
                                <td class="p-4">{{ p.jabatan }}</td>
                                <td class="p-4">
                                    <span :class="p.status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ p.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button @click="editPejabat(p)" class="text-amber-500 hover:text-amber-600 transition"><i class="fas fa-edit"></i></button>
                                    <a :href="'<?php echo site_url('sk_editor/delete_pejabat/'); ?>' + p.id" onclick="return confirm('Are you sure?')" class="text-red-500 hover:text-red-600 transition"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <tr v-if="pejabatList.length === 0">
                                <td colspan="5" class="p-8 text-center text-gray-500">No data found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const { createApp, ref, reactive, onMounted } = Vue;
            
            // Default Constants
            const DEFAULT_SETTINGS = {
                paperSize: 'A4', 
                orientation: 'portrait',
                marginTop: 20, marginBottom: 20, marginLeft: 25, marginRight: 20,
                showKop: true,
                kopLogo: 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Logo_of_the_Ministry_of_Religious_Affairs_of_the_Republic_of_Indonesia.svg/1200px-Logo_of_the_Ministry_of_Religious_Affairs_of_the_Republic_of_Indonesia.svg.png',
                kopTitle1: 'MAHKAMAH AGUNG REPUBLIK INDONESIA',
                kopTitle2: 'DIREKTORAT JENDERAL BADAN PERADILAN AGAMA',
                kopTitle3: 'PENGADILAN TINGGI AGAMA GORONTALO',
                kopTitle4: 'PENGADILAN AGAMA GORONTALO',
                kopAddress: 'Jalan Achmad Nadjamuddin No.22, Dulalowo Timur, Kecamatan Kota Tengah\nKota Gorontalo, 96138. www.pa-gorontalo.go.id, surat@pa-gorontalo.go.id'
            };

            const activeTab = ref('general'); // Default to General now
            const isDarkMode = ref(localStorage.getItem('sk_editor_theme') === 'dark');
            const pejabatList = ref(<?php echo isset($pejabat) ? json_encode($pejabat) : '[]'; ?>);
            
            const generalSettings = reactive({ ...DEFAULT_SETTINGS });

            const form = ref({ id: '', nama: '', nip: '', jabatan: '', status: 'aktif' });
            const isEditing = ref(false);

            onMounted(() => {
                if (isDarkMode.value) document.documentElement.classList.add('dark');
                
                // Load saved settings
                const saved = localStorage.getItem('sk_editor_settings');
                if (saved) {
                    try {
                        Object.assign(generalSettings, JSON.parse(saved));
                    } catch(e) { console.error('Error loading settings', e); }
                }
            });

            const saveGeneralSettings = () => {
                localStorage.setItem('sk_editor_settings', JSON.stringify(generalSettings));
                toastr.success('Default Global Settings Saved!');
            };

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

            const editPejabat = (p) => {
                form.value = { ...p };
                isEditing.value = true;
                activeTab.value = 'pejabat'; // Switch tab
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            const resetForm = () => {
                form.value = { id: '', nama: '', nip: '', jabatan: '', status: 'aktif' };
                isEditing.value = false;
            };

            return {
                activeTab, isDarkMode, toggleTheme,
                pejabatList, form, isEditing, editPejabat, resetForm,
                generalSettings, saveGeneralSettings
            };
        }
    }).mount('#app');
</script>
</body>
</html>
