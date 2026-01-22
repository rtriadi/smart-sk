<!-- Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
    var ARCHIVES_DATA = <?php echo json_encode($archives); ?>;
    var SITE_URL = '<?php echo rtrim(site_url(), "/") . "/"; ?>';
</script>
<style>
    [v-cloak] { display: none; }
</style>

<div id="app" v-cloak>
    
    <!-- Title & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Saved Drafts</h1>
            <p class="text-slate-500 text-sm">Manage your generated Surat Keputusan</p>
        </div>
        
        <!-- Search -->
        <div class="relative max-w-md w-full md:w-auto">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-slate-400"></i>
            </span>
            <input type="text" v-model="searchQuery" placeholder="Search drafts..." 
                class="w-full md:w-64 bg-white border border-slate-300 text-slate-900 rounded-lg pl-10 pr-4 py-2 text-sm shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <th class="p-4">No. Surat</th>
                    <th class="p-4">Template</th>
                    <th class="p-4">Created At</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr v-for="a in filteredArchives" :key="a.id" class="hover:bg-slate-50 transition duration-150">
                    <td class="p-4 font-medium text-slate-900">{{ a.no_surat }}</td>
                    <td class="p-4 text-slate-600">
                         <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                            {{ a.nama_sk }}
                        </span>
                    </td>
                    <td class="p-4 text-slate-500 text-sm font-mono">{{ a.created_at }}</td>
                    <td class="p-4 text-right space-x-2">
                        <a :href="editUrl(a.id)" class="text-amber-500 hover:text-amber-600 transition inline-block p-1" title="Edit Draft">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button @click="renameDraft(a)" class="text-blue-500 hover:text-blue-600 transition inline-block p-1" title="Rename Draft">
                            <i class="fas fa-pen"></i>
                        </button>
                        <a :href="cloneUrl(a.id)" class="text-purple-500 hover:text-purple-600 transition inline-block p-1" title="Duplicate Draft" onclick="return confirm('Duplicate this draft?');">
                            <i class="fas fa-copy"></i>
                        </a>
                        <a :href="printUrl(a.id)" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition inline-block p-1" title="Print PDF">
                            <i class="fas fa-print"></i>
                        </a>
                        <button @click="deleteDraft(a.id)" class="text-red-500 hover:text-red-600 transition inline-block p-1" title="Delete Draft">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr v-if="filteredArchives.length === 0">
                    <td colspan="4" class="p-12 text-center text-slate-500">
                       <div class="mb-3"><i class="fas fa-folder-open text-3xl text-slate-300"></i></div>
                        <p>No drafts found matching your search.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!-- Vue App Logic -->
<script src="<?php echo base_url('assets/js/archive_manager_vue.js?v=' . time()); ?>"></script>
