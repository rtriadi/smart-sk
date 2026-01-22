<!-- Dependencies not in layout -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
    var TEMPLATES_DATA = <?php echo json_encode($templates); ?>;
    var SITE_URL = '<?php echo site_url(); ?>';
</script>

<div id="app" v-cloak>
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Templates Manager</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your document templates</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a :href="createUrl()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg shadow-sm font-medium transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Create New
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Template Name</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Pattern</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="t in filteredTemplates" :key="t.id" class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-4 py-2.5 font-medium text-slate-900">{{ t.nama_sk }}</td>
                        <td class="px-4 py-2.5">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full border border-indigo-100">
                                {{ t.kategori }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 font-mono text-xs">{{ t.nomor_pattern }}</td>
                        <td class="px-4 py-2.5 text-right space-x-2">
                            <a :href="editUrl(t.id)" class="text-slate-400 hover:text-blue-600 transition inline-block p-1" title="Edit">
                                <i class="fas fa-pen"></i>
                            </a>
                            <a :href="deleteUrl(t.id)" @click="confirmDelete" class="text-slate-400 hover:text-red-600 transition inline-block p-1" title="Delete">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <tr v-if="filteredTemplates.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                            No templates found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/js/template_manager_vue.js'); ?>"></script>
