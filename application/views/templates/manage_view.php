<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    var TEMPLATES_DATA = <?php echo json_encode($templates); ?>;
    var SITE_URL = '<?php echo rtrim(site_url(), "/") . "/"; ?>';
    
    // Flash messages
    var FLASH_SUCCESS = '<?php echo $this->session->flashdata('success'); ?>';
    var FLASH_ERROR = '<?php echo $this->session->flashdata('error'); ?>';
</script>

<style>
    [v-cloak] { display: none; }
</style>

<div id="app" v-cloak class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Template Manager</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola template dokumen Surat Keputusan</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative">
                <input type="text" v-model="searchQuery" 
                    class="pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500 w-64 bg-white shadow-sm"
                    placeholder="Cari template...">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            </div>
            <!-- Create Button -->
            <a :href="createUrl()" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2 rounded-lg shadow-sm font-medium transition flex items-center gap-2">
                <i class="fas fa-plus"></i> 
                <span>Buat Template</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Total Template</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ templates.length }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-alt text-teal-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Kategori</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ uniqueCategories.length }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-folder text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-medium">Hasil Filter</p>
                    <p class="text-3xl font-bold text-slate-800 mt-1">{{ filteredTemplates.length }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-filter text-amber-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Filter Chips -->
    <div class="flex flex-wrap gap-2" v-if="uniqueCategories.length > 0">
        <button @click="filterCategory = ''" 
            :class="['px-3 py-1.5 rounded-full text-sm font-medium transition-all', 
                filterCategory === '' ? 'bg-teal-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']">
            Semua
        </button>
        <button v-for="cat in uniqueCategories" :key="cat" @click="filterCategory = cat"
            :class="['px-3 py-1.5 rounded-full text-sm font-medium transition-all', 
                filterCategory === cat ? 'bg-teal-600 text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200']">
            {{ cat }}
        </button>
    </div>

    <!-- Templates Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Nama Template</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Pola Nomor</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="t in filteredTemplates" :key="t.id" class="hover:bg-slate-50 transition-colors duration-150 group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                    {{ t.nama_sk.charAt(0).toUpperCase() }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ t.nama_sk }}</p>
                                    <p class="text-xs text-slate-400">ID: {{ t.id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-indigo-100">
                                <i class="fas fa-tag text-[10px]"></i>
                                {{ t.kategori }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <code class="text-slate-600 font-mono text-xs bg-slate-100 px-2 py-1 rounded">{{ t.nomor_pattern || '-' }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <!-- Use Template -->
                                <a :href="useUrl(t.id)" 
                                    class="p-2 text-teal-600 hover:text-white hover:bg-teal-600 rounded-lg transition-all" 
                                    title="Gunakan Template">
                                    <i class="fas fa-file-signature"></i>
                                </a>
                                <!-- Edit -->
                                <a :href="editUrl(t.id)" 
                                    class="p-2 text-amber-500 hover:text-white hover:bg-amber-500 rounded-lg transition-all" 
                                    title="Edit Template">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <!-- Delete -->
                                <a :href="deleteUrl(t.id)" @click="confirmDelete" 
                                    class="p-2 text-red-500 hover:text-white hover:bg-red-500 rounded-lg transition-all" 
                                    title="Hapus Template">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredTemplates.length === 0">
                        <td colspan="4" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-file-alt text-slate-400 text-2xl"></i>
                                </div>
                                <p class="text-slate-500 font-medium">Tidak ada template ditemukan</p>
                                <p class="text-slate-400 text-sm mt-1">Coba ubah filter atau kata kunci pencarian</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        const templates = ref(TEMPLATES_DATA || []);
        const searchQuery = ref('');
        const filterCategory = ref('');

        // Show flash messages on mount
        onMounted(() => {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 4000
            };
            
            if (FLASH_SUCCESS && FLASH_SUCCESS.trim()) {
                toastr.success(FLASH_SUCCESS);
            }
            if (FLASH_ERROR && FLASH_ERROR.trim()) {
                toastr.error(FLASH_ERROR);
            }
        });

        const uniqueCategories = computed(() => {
            const cats = templates.value.map(t => t.kategori).filter(Boolean);
            return [...new Set(cats)];
        });

        const filteredTemplates = computed(() => {
            return templates.value.filter(t => {
                const matchesSearch = !searchQuery.value || 
                    t.nama_sk.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    (t.nomor_pattern && t.nomor_pattern.toLowerCase().includes(searchQuery.value.toLowerCase()));
                const matchesCategory = !filterCategory.value || t.kategori === filterCategory.value;
                return matchesSearch && matchesCategory;
            });
        });

        const createUrl = () => SITE_URL + 'templates/create';
        const editUrl = (id) => SITE_URL + 'templates/edit/' + id;
        const useUrl = (id) => SITE_URL + 'sk_editor/create/' + id;
        const deleteUrl = (id) => SITE_URL + 'templates/delete/' + id;

        const confirmDelete = (e) => {
            if (!confirm('Apakah Anda yakin ingin menghapus template ini? Tindakan ini tidak dapat dibatalkan.')) {
                e.preventDefault();
            }
        };

        return {
            templates,
            searchQuery,
            filterCategory,
            uniqueCategories,
            filteredTemplates,
            createUrl,
            editUrl,
            useUrl,
            deleteUrl,
            confirmDelete
        };
    }
}).mount('#app');
</script>
