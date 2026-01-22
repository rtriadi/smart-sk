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
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Document Archives</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your generated Surat Keputusan</p>
        </div>
        
        <!-- Search -->
        <div class="mt-4 md:mt-0 relative max-w-xs w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-slate-400"></i>
            </span>
            <input type="text" v-model="searchQuery" placeholder="Search archives..." 
                class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg pl-10 pr-4 py-2 text-sm shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">No. Surat</th>
                        <th class="px-4 py-3">Template</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="a in filteredArchives" :key="a.id" class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-4 py-2.5 font-medium text-slate-900">{{ a.no_surat }}</td>
                        <td class="px-4 py-2.5">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full border border-indigo-100">
                                {{ a.nama_sk }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 font-mono text-xs">{{ a.created_at }}</td>
                        <td class="px-4 py-2.5 text-right space-x-2">
                            <!-- Edit -->
                            <a :href="editUrl(a.id)" class="text-slate-400 hover:text-amber-500 transition inline-block p-1" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <!-- Clone -->
                            <a :href="cloneUrl(a.id)" class="text-slate-400 hover:text-purple-600 transition inline-block p-1" title="Clone" onclick="return confirm('Duplicate this draft?');">
                                <i class="fas fa-copy"></i>
                            </a>
                            <!-- Print -->
                            <a :href="printUrl(a.id)" target="_blank" class="text-slate-400 hover:text-emerald-600 transition inline-block p-1" title="Print">
                                <i class="fas fa-print"></i>
                            </a>
                            <!-- Delete -->
                            <button @click="deleteDraft(a.id)" class="text-slate-400 hover:text-red-600 transition inline-block p-1" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredArchives.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                           <div class="mb-2"><i class="fas fa-folder-open text-2xl text-slate-300"></i></div>
                            <p>No archives found.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Vue App Logic -->
<script src="<?php echo base_url('assets/js/archive_manager_vue.js?v=' . time()); ?>"></script>
