<!-- Dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
            <h1 class="text-2xl font-bold text-slate-800">Arsip Dokumen</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola draft dan SK final Anda</p>
        </div>
        
        <!-- Search -->
        <div class="mt-4 md:mt-0 relative max-w-xs w-full">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-slate-400"></i>
            </span>
            <input type="text" v-model="searchQuery" placeholder="Cari dokumen..." 
                class="w-full bg-white border border-slate-300 text-slate-900 rounded-lg pl-10 pr-4 py-2 text-sm shadow-sm focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:outline-none transition-all">
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex border-b border-slate-200 mb-6">
        <button @click="activeTab = 'drafts'" 
                :class="activeTab === 'drafts' ? 'text-teal-600 border-teal-500 bg-teal-50' : 'text-slate-500 border-transparent hover:text-slate-700'"
                class="px-6 py-3 text-sm font-semibold border-b-2 transition-all flex items-center gap-2">
            <i class="fas fa-file-alt"></i> 
            Draft
            <span v-if="draftArchives.length > 0" class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full">{{ draftArchives.length }}</span>
        </button>
        <button @click="activeTab = 'final'" 
                :class="activeTab === 'final' ? 'text-teal-600 border-teal-500 bg-teal-50' : 'text-slate-500 border-transparent hover:text-slate-700'"
                class="px-6 py-3 text-sm font-semibold border-b-2 transition-all flex items-center gap-2">
            <i class="fas fa-check-circle"></i> 
            SK Final
            <span v-if="finalArchives.length > 0" class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">{{ finalArchives.length }}</span>
        </button>
    </div>

    <!-- Draft Table -->
    <div v-show="activeTab === 'drafts'" class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Kode Draft</th>
                        <th class="px-4 py-3">Template</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="a in filteredDrafts" :key="a.id" class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-slate-900">{{ a.no_surat }}</span>
                                <span class="bg-amber-100 text-amber-700 text-[10px] px-1.5 py-0.5 rounded font-semibold">DRAFT</span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full border border-indigo-100">
                                {{ a.nama_sk }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 font-mono text-xs">{{ a.created_at }}</td>
                        <td class="px-4 py-2.5 text-right space-x-1">
                            <!-- Rename -->
                            <button @click="renameDraft(a)" class="text-slate-500 hover:text-teal-600 transition inline-block p-1.5 hover:bg-teal-50 rounded" title="Ubah Nama">
                                <i class="fas fa-i-cursor"></i>
                            </button>
                            <!-- Edit -->
                            <a :href="editUrl(a.id)" class="text-amber-500 hover:text-amber-600 transition inline-block p-1.5 hover:bg-amber-50 rounded" title="Edit Draft">
                                <i class="fas fa-pen"></i>
                            </a>
                            <!-- Finalize -->
                            <button @click="finalizeDraft(a)" class="text-green-500 hover:text-green-600 transition inline-block p-1.5 hover:bg-green-50 rounded" title="Jadikan Final">
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <!-- Clone -->
                            <a :href="cloneUrl(a.id)" class="text-indigo-500 hover:text-indigo-600 transition inline-block p-1.5 hover:bg-indigo-50 rounded" title="Duplikat" onclick="return confirm('Duplikat draft ini?');">
                                <i class="fas fa-copy"></i>
                            </a>
                            <!-- Print -->
                            <a :href="printUrl(a.id)" target="_blank" class="text-sky-500 hover:text-sky-600 transition inline-block p-1.5 hover:bg-sky-50 rounded" title="Cetak">
                                <i class="fas fa-print"></i>
                            </a>
                            <!-- Delete -->
                            <button @click="deleteDraft(a.id)" class="text-red-500 hover:text-red-600 transition inline-block p-1.5 hover:bg-red-50 rounded" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <tr v-if="filteredDrafts.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                           <div class="mb-2"><i class="fas fa-file-alt text-2xl text-slate-300"></i></div>
                            <p>Tidak ada draft ditemukan.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Final SK Table -->
    <div v-show="activeTab === 'final'" class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-4 py-3">Nomor Surat</th>
                        <th class="px-4 py-3">Template</th>
                        <th class="px-4 py-3">Tanggal Final</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="a in filteredFinal" :key="a.id" class="hover:bg-slate-50 transition-colors duration-150">
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-slate-900">{{ a.no_surat }}</span>
                                <span class="bg-green-100 text-green-700 text-[10px] px-1.5 py-0.5 rounded font-semibold">FINAL</span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="bg-indigo-50 text-indigo-700 text-xs font-semibold px-2 py-1 rounded-full border border-indigo-100">
                                {{ a.nama_sk }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-slate-600 font-mono text-xs">{{ a.finalized_at || a.updated_at || a.created_at }}</td>
                        <td class="px-4 py-2.5 text-right space-x-1">
                            <!-- View/Edit -->
                            <a :href="editUrl(a.id)" class="text-amber-500 hover:text-amber-600 transition inline-block p-1.5 hover:bg-amber-50 rounded" title="Lihat/Edit">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Revert to Draft -->
                            <button @click="revertToDraft(a)" class="text-amber-500 hover:text-amber-600 transition inline-block p-1.5 hover:bg-amber-50 rounded" title="Kembalikan ke Draft">
                                <i class="fas fa-undo"></i>
                            </button>
                            <!-- Clone -->
                            <a :href="cloneUrl(a.id)" class="text-indigo-500 hover:text-indigo-600 transition inline-block p-1.5 hover:bg-indigo-50 rounded" title="Duplikat" onclick="return confirm('Duplikat SK ini?');">
                                <i class="fas fa-copy"></i>
                            </a>
                            <!-- Print -->
                            <a :href="printUrl(a.id)" target="_blank" class="text-sky-500 hover:text-sky-600 transition inline-block p-1.5 hover:bg-sky-50 rounded" title="Cetak">
                                <i class="fas fa-print"></i>
                            </a>
                            <!-- PDF -->
                            <a :href="pdfUrl(a.id)" target="_blank" class="text-red-500 hover:text-red-600 transition inline-block p-1.5 hover:bg-red-50 rounded" title="Download PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </td>
                    </tr>
                    <tr v-if="filteredFinal.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-slate-500">
                           <div class="mb-2"><i class="fas fa-check-circle text-2xl text-slate-300"></i></div>
                            <p>Belum ada SK yang difinalkan.</p>
                            <p class="text-xs mt-1">Finalisasi draft untuk memindahkannya ke sini.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Vue App Logic -->
<script>
const { createApp, ref, computed, onMounted } = Vue;

createApp({
    setup() {
        // State
        const archives = ref(ARCHIVES_DATA);
        const siteUrl = ref(SITE_URL);
        const searchQuery = ref('');
        const activeTab = ref('drafts');

        // Computed - Split by status (draft starts with DRAFT- or has status = draft)
        const draftArchives = computed(() => {
            return archives.value.filter(a => 
                (a.status === 'draft' || a.status === null || a.status === undefined) && 
                (a.no_surat && a.no_surat.toUpperCase().startsWith('DRAFT'))
            );
        });

        const finalArchives = computed(() => {
            return archives.value.filter(a => 
                a.status === 'final' || 
                (a.no_surat && !a.no_surat.toUpperCase().startsWith('DRAFT'))
            );
        });

        const filteredDrafts = computed(() => {
            if (!searchQuery.value) return draftArchives.value;
            const lowerQuery = searchQuery.value.toLowerCase();
            return draftArchives.value.filter(a =>
                a.no_surat.toLowerCase().includes(lowerQuery) ||
                a.nama_sk.toLowerCase().includes(lowerQuery)
            );
        });

        const filteredFinal = computed(() => {
            if (!searchQuery.value) return finalArchives.value;
            const lowerQuery = searchQuery.value.toLowerCase();
            return finalArchives.value.filter(a =>
                a.no_surat.toLowerCase().includes(lowerQuery) ||
                a.nama_sk.toLowerCase().includes(lowerQuery)
            );
        });

        // Methods
        const editUrl = (id) => `${siteUrl.value}sk_editor/edit_draft/${id}`;
        const printUrl = (id) => `${siteUrl.value}sk_editor/print_draft/${id}`;
        const pdfUrl = (id) => `${siteUrl.value}sk_editor/generate_pdf/${id}`;
        const cloneUrl = (id) => `${siteUrl.value}sk_editor/clone_draft/${id}`;

        const deleteDraft = (id) => {
            if (confirm('Apakah Anda yakin ingin menghapus draft ini? Tindakan ini tidak dapat dibatalkan.')) {
                window.location.href = `${siteUrl.value}sk_editor/delete_draft/${id}`;
            }
        };

        const renameDraft = async (item) => {
            const newName = prompt("Ubah Nama Draft:", item.no_surat);
            if (newName && newName !== item.no_surat) {
                try {
                    const params = new URLSearchParams();
                    params.append('id', item.id);
                    params.append('name', newName);

                    const response = await fetch(`${siteUrl.value}sk_editor/rename_draft`, {
                        method: 'POST',
                        body: params
                    });
                    const res = await response.json();

                    if (res.status === 'success') {
                        item.no_surat = newName;
                        toastr.success('Nama draft berhasil diubah');
                    } else {
                        toastr.error('Gagal mengubah nama: ' + (res.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error(e);
                    toastr.error('Error mengubah nama draft');
                }
            }
        };

        const finalizeDraft = async (item) => {
            const noSurat = prompt("Masukkan Nomor Surat Final:", item.no_surat.replace('DRAFT-', ''));
            if (noSurat && noSurat.trim()) {
                try {
                    const params = new URLSearchParams();
                    params.append('id', item.id);
                    params.append('no_surat', noSurat.trim());
                    params.append('status', 'final');

                    const response = await fetch(`${siteUrl.value}sk_editor/finalize_draft`, {
                        method: 'POST',
                        body: params
                    });
                    const res = await response.json();

                    if (res.status === 'success') {
                        item.no_surat = noSurat.trim();
                        item.status = 'final';
                        toastr.success('SK berhasil difinalkan!');
                        // Refresh to update tabs
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        toastr.error('Gagal memfinalkan: ' + (res.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error(e);
                    toastr.error('Error memfinalkan draft');
                }
            }
        };

        const revertToDraft = async (item) => {
            if (!confirm('Kembalikan SK ini ke status Draft?')) return;
            
            try {
                const params = new URLSearchParams();
                params.append('id', item.id);
                params.append('status', 'draft');

                const response = await fetch(`${siteUrl.value}sk_editor/update_status`, {
                    method: 'POST',
                    body: params
                });
                const res = await response.json();

                if (res.status === 'success') {
                    item.status = 'draft';
                    item.no_surat = 'DRAFT-' + item.no_surat;
                    toastr.success('SK dikembalikan ke draft');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastr.error('Gagal: ' + (res.message || 'Unknown error'));
                }
            } catch (e) {
                console.error(e);
                toastr.error('Error mengubah status');
            }
        };

        // Toast config
        onMounted(() => {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right',
                timeOut: 3000
            };
        });

        return {
            archives,
            searchQuery,
            activeTab,
            draftArchives,
            finalArchives,
            filteredDrafts,
            filteredFinal,
            editUrl,
            printUrl,
            pdfUrl,
            cloneUrl,
            deleteDraft,
            renameDraft,
            finalizeDraft,
            revertToDraft
        };
    }
}).mount('#app');
</script>
