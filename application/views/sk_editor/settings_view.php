<!-- Dependencies required for this view -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<style>[v-cloak] { display: none; }</style>

<div id="settings-app" v-cloak>
    
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Settings</h1>
        <!-- Theme toggle removed to align with Enterprise Layout -->
    </div>

    <div class="grid grid-cols-12 gap-8">
        <!-- Sidebar Tabs -->
        <div class="col-span-12 md:col-span-3">
            <nav class="space-y-1">
                <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-500' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-3 font-medium transition flex items-center bg-white rounded-r-md">
                    <i class="fas fa-sliders-h w-6"></i> General
                </button>
                <button @click="activeTab = 'pejabat'" :class="activeTab === 'pejabat' ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-500' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-3 font-medium transition flex items-center bg-white rounded-r-md">
                    <i class="fas fa-user-tie w-6"></i> Master Pejabat
                </button>
                <button @click="activeTab = 'kategori'" :class="activeTab === 'kategori' ? 'bg-teal-50 text-teal-700 border-l-4 border-teal-500' : 'text-slate-600 hover:bg-slate-50'" class="w-full text-left px-4 py-3 font-medium transition flex items-center bg-white rounded-r-md">
                    <i class="fas fa-tags w-6"></i> Master Kategori
                </button>
            </nav>
        </div>

        <!-- Content -->
        <div class="col-span-12 md:col-span-9">
            
            <!-- General Tab -->
            <div v-if="activeTab === 'general'" class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-lg font-bold mb-6 text-slate-800">General Settings (Default)</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Paper & Margins -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Paper</h3>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Default Paper Size</label>
                            <select v-model="generalSettings.paperSize" class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                                <option value="A4">A4</option>
                                <option value="F4">F4</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Default Orientation</label>
                            <select v-model="generalSettings.orientation" class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                                <option value="portrait">Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>
                        
                        <div class="pt-2">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Default Margins (mm)</label>
                            <div class="grid grid-cols-4 gap-2">
                                <div><label class="text-xs text-center block text-slate-500">Top</label><input type="number" v-model="generalSettings.marginTop" class="w-full text-center rounded-md border-slate-300 px-2 py-2 text-sm"></div>
                                <div><label class="text-xs text-center block text-slate-500">Btm</label><input type="number" v-model="generalSettings.marginBottom" class="w-full text-center rounded-md border-slate-300 px-2 py-2 text-sm"></div>
                                <div><label class="text-xs text-center block text-slate-500">Left</label><input type="number" v-model="generalSettings.marginLeft" class="w-full text-center rounded-md border-slate-300 px-2 py-2 text-sm"></div>
                                <div><label class="text-xs text-center block text-slate-500">Right</label><input type="number" v-model="generalSettings.marginRight" class="w-full text-center rounded-md border-slate-300 px-2 py-2 text-sm"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kop Surat Removed -->

                    
                    <!-- Typography & Extras -->
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-2">Typography & Extras</h3>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Font Size</label>
                                <select v-model="generalSettings.fontSize" class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="10pt">10pt</option>
                                    <option value="11pt">11pt</option>
                                    <option value="12pt">12pt</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Row Height</label>
                                <select v-model="generalSettings.lineHeight" class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                                    <option value="1.0">1.0</option>
                                    <option value="1.15">1.15</option>
                                    <option value="1.5">1.5</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-slate-700">Show Page Numbers</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="generalSettings.showPageNumbers" class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-teal-600"></div>
                            </label>
                        </div>

                         <div class="pt-4 mt-2 border-t border-slate-100">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Default Content Logo (Isi SK)</label>
                            <!-- File Upload for Global SK Logo -->
                            <input type="file" @change="handleContentLogoUpload" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 mb-2">
                            
                            <!-- Preview -->
                            <div v-if="generalSettings.defaultSkLogo" class="mt-2 bg-white p-2 rounded border border-slate-200 inline-block">
                                <img :src="generalSettings.defaultSkLogo" class="max-h-16 object-contain mx-auto">
                            </div>
                            <button v-if="generalSettings.defaultSkLogo" @click="generalSettings.defaultSkLogo = ''" class="ml-2 text-xs text-red-500 hover:text-red-700">Remove</button>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end pt-6 border-t border-slate-200">
                    <button @click="saveGeneralSettings" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded shadow-md font-bold transition flex items-center">
                        <i class="fas fa-save mr-2"></i> Save Defaults
                    </button>
                </div>
            </div>

            <!-- Pejabat Tab -->
            <div v-if="activeTab === 'pejabat'" class="space-y-6">
                
                <!-- Add New -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold mb-4 text-slate-800">{{ isEditing ? 'Edit Pejabat' : 'Add New Pejabat' }}</h2>
                    <form action="<?php echo site_url('sk_editor/save_pejabat'); ?>" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <input type="hidden" name="id" v-model="form.id">
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" v-model="form.nama" required class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. Drs. H. Fulan, M.H.">
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">NIP</label>
                            <input type="text" name="nip" v-model="form.nip" class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. 19900101...">
                        </div>
                        
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
                            <input type="text" name="jabatan" v-model="form.jabatan" required class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. Ketua">
                        </div>

                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                            <select name="status" v-model="form.status" class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500">
                                <option value="aktif">Aktif</option>
                                <option value="non-aktif">Non-Aktif</option>
                            </select>
                        </div>
                        
                        <div class="col-span-2 flex justify-end space-x-2 mt-2">
                            <button type="button" @click="resetForm" v-if="isEditing" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-md hover:bg-teal-700">
                                {{ isEditing ? 'Update Pejabat' : 'Save Pejabat' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="p-4">Nama</th>
                                <th class="p-4">NIP</th>
                                <th class="p-4">Jabatan</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="p in pejabatList" :key="p.id" class="hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-700">{{ p.nama }}</td>
                                <td class="p-4 font-mono text-sm text-slate-500">{{ p.nip || '-' }}</td>
                                <td class="p-4 text-slate-700">{{ p.jabatan }}</td>
                                <td class="p-4">
                                    <span :class="p.status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-800'" class="px-2 py-1 rounded-full text-xs font-semibold">
                                        {{ p.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <a :href="'<?php echo site_url('sk_editor/set_default_pejabat/'); ?>' + p.id" class="transition" :class="p.is_default == 1 ? 'text-yellow-500 hover:text-yellow-600' : 'text-slate-300 hover:text-yellow-400'" title="Set as Default">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <button @click="editPejabat(p)" class="text-amber-500 hover:text-amber-600 transition"><i class="fas fa-edit"></i></button>
                                    <a :href="'<?php echo site_url('sk_editor/delete_pejabat/'); ?>' + p.id" onclick="return confirm('Are you sure?')" class="text-red-500 hover:text-red-600 transition"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <tr v-if="pejabatList.length === 0">
                                <td colspan="5" class="p-8 text-center text-slate-500">No data found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Kategori Tab -->
            <div v-if="activeTab === 'kategori'" class="space-y-6">
                <!-- Add New -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-lg font-bold mb-4 text-slate-800">{{ isEditingCat ? 'Edit Kategori' : 'Add New Kategori' }}</h2>
                    <form action="<?php echo site_url('sk_editor/save_category'); ?>" method="POST" class="grid grid-cols-1 gap-4">
                        <input type="hidden" name="id" v-model="catForm.id">
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
                            <input type="text" name="category_name" v-model="catForm.category_name" required class="w-full rounded-md border-slate-300 px-3 py-2 text-sm focus:ring-teal-500 focus:border-teal-500" placeholder="e.g. SK Pensiun">
                        </div>
                        
                        <div class="flex justify-end space-x-2 mt-2">
                            <button type="button" @click="resetCatForm" v-if="isEditingCat" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-md hover:bg-teal-700">
                                {{ isEditingCat ? 'Update Kategori' : 'Save Kategori' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- List -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                            <tr>
                                <th class="p-4">Nama Kategori</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="c in categoryList" :key="c.id" class="hover:bg-slate-50">
                                <td class="p-4 font-medium text-slate-700">{{ c.category_name }}</td>
                                <td class="p-4 text-right space-x-2">
                                    <button @click="editCategory(c)" class="text-amber-500 hover:text-amber-600 transition"><i class="fas fa-edit"></i></button>
                                    <a :href="'<?php echo site_url('sk_editor/delete_category/'); ?>' + c.id" onclick="return confirm('Are you sure?')" class="text-red-500 hover:text-red-600 transition"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <tr v-if="categoryList.length === 0">
                                <td colspan="2" class="p-8 text-center text-slate-500">No categories found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const { createApp, ref, reactive, onMounted } = Vue;

    createApp({
        setup() {
            // Default Constants
            const DEFAULT_SETTINGS = {
                paperSize: 'A4', 
                orientation: 'portrait',
                marginTop: 20, marginBottom: 20, marginLeft: 20, marginRight: 20,
                fontSize: '12pt', lineHeight: '1.5', showPageNumbers: false,
                defaultSkLogo: ''
            };

            const activeTab = ref('general');
            const pejabatList = ref(<?php echo isset($pejabat) ? json_encode($pejabat) : '[]'; ?>);
            const categoryList = ref(<?php echo isset($categories) ? json_encode($categories) : '[]'; ?>);
            
            const generalSettings = reactive({ ...DEFAULT_SETTINGS });

            const form = ref({ id: '', nama: '', nip: '', jabatan: '', status: 'aktif' });
            const catForm = ref({ id: '', category_name: '' });
            const isEditing = ref(false);
            const isEditingCat = ref(false);

            onMounted(() => {
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

            const editPejabat = (p) => {
                form.value = { ...p };
                isEditing.value = true;
                activeTab.value = 'pejabat'; 
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            const resetForm = () => {
                form.value = { id: '', nama: '', nip: '', jabatan: '', status: 'aktif' };
                isEditing.value = false;
            };

            const editCategory = (c) => {
                catForm.value = { ...c };
                isEditingCat.value = true;
                activeTab.value = 'kategori'; 
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            const resetCatForm = () => {
                catForm.value = { id: '', category_name: '' };
                isEditingCat.value = false;
            };

            const compressImage = (file, maxWidth, maxHeight, quality, callback) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        let width = img.width;
                        let height = img.height;

                        if (width > height) {
                            if (width > maxWidth) {
                                height *= maxWidth / width;
                                width = maxWidth;
                            }
                        } else {
                            if (height > maxHeight) {
                                width *= maxHeight / height;
                                height = maxHeight;
                            }
                        }

                        canvas.width = width;
                        canvas.height = height;
                        ctx.drawImage(img, 0, 0, width, height);

                        let dataUrl = canvas.toDataURL('image/webp', quality);
                        
                        if (dataUrl.length > 500000) {
                             const scale = 0.7;
                             canvas.width = width * scale;
                             canvas.height = height * scale;
                             ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                             dataUrl = canvas.toDataURL('image/webp', quality);
                        }

                        callback(dataUrl);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            };

            const handleContentLogoUpload = (event) => {
                 const file = event.target.files[0];
                 if (file) {
                     compressImage(file, 300, 300, 0.7, (dataUrl) => {
                        generalSettings.defaultSkLogo = dataUrl;
                    });
                 }
            };

            return {
                activeTab, 
                pejabatList, categoryList, form, catForm, isEditing, isEditingCat, 
                editPejabat, resetForm, editCategory, resetCatForm,
                generalSettings, saveGeneralSettings, handleContentLogoUpload
            };

        }
    }).mount('#settings-app');
</script>