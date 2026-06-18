
<!-- TinyMCE (Vue already loaded by enterprise_layout) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    var TEMPLATE_DATA = {}; // Empty for create
    var TEMPLATE_INITIAL_DATA = TEMPLATE_DATA;
    var CATEGORIES = <?php echo json_encode($categories); ?>;
    var SITE_URL = '<?php echo site_url(); ?>';
    var IS_EDIT = false;
</script>

<style>
    [v-cloak] { display: none; }
</style>

<div class="space-y-6">
    <!-- Vue App for Header/Form -->
    <div id="template-app" v-cloak>
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div class="flex items-center gap-4">
                <a href="<?php echo site_url('templates'); ?>" 
                    class="p-2 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all" 
                    title="Kembali">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Buat Template Baru</h1>
                    <p class="text-slate-500 text-sm mt-0.5">Desain template Surat Keputusan dengan visual editor</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showHelp = true" 
                    class="px-4 py-2 text-slate-600 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all flex items-center gap-2 border border-slate-200">
                    <i class="fas fa-question-circle"></i>
                    <span>Panduan</span>
                </button>
                <button @click="saveTemplate" 
                    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg font-medium transition shadow-md shadow-teal-500/20 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Template</span>
                </button>
            </div>
        </div>

        <!-- Template Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
            <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Informasi Template</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nama Template <span class="text-red-500">*</span></label>
                    <input type="text" v-model="form.nama_sk" 
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition" 
                        placeholder="Contoh: SK Pemberhentian Pegawai">
                </div>
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select v-model="form.kategori" 
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition bg-white">
                        <option value="" disabled>Pilih Kategori</option>
                        <option v-for="c in categories" :key="c.id" :value="c.category_name">{{ c.category_name }}</option>
                    </select>
                </div>
                <!-- Pattern -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pola Nomor Surat</label>
                    <input type="text" v-model="form.nomor_pattern" 
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition font-mono text-sm" 
                        placeholder="Contoh: W26-A/SK/{nomor}/{bulan}/{tahun}">
                    <p class="text-xs text-slate-400 mt-1">Gunakan {bulan}, {tahun}, {nomor} untuk auto-replacement</p>
                </div>
            </div>
        </div>

        <!-- Hidden Form for Submission -->
        <form id="template-form" method="POST" action="<?php echo site_url('templates/store'); ?>" style="display: none;">
            <input type="hidden" name="nama_sk" id="input_nama_sk">
            <input type="hidden" name="kategori" id="input_kategori">
            <input type="hidden" name="nomor_pattern" id="input_nomor_pattern">
            <input type="hidden" name="form_config" id="form_config">
            <input type="hidden" name="html_pattern" id="html_pattern">
        </form>

        <!-- Help Modal -->
        <div v-if="showHelp" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-[70] p-4" @click.self="showHelp = false">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div class="flex justify-between items-center px-8 py-5 border-b border-slate-200 bg-gradient-to-r from-teal-600 to-teal-500">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Panduan Pembuatan Template SK</h2>
                        <p class="text-teal-100 text-sm mt-1">Pelajari cara membuat template dokumen yang dinamis</p>
                    </div>
                    <button @click="showHelp = false" class="text-white/80 hover:text-white hover:bg-white/20 p-2 rounded-lg transition">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="p-8 overflow-y-auto flex-1 space-y-8">
                    
                    <!-- Quick Start -->
                    <div class="bg-gradient-to-r from-teal-50 to-cyan-50 rounded-xl p-6 border border-teal-100">
                        <h3 class="text-lg font-bold text-teal-700 mb-3 flex items-center gap-2">
                            <i class="fas fa-rocket"></i> Alur Kerja (Workflow)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white rounded-lg p-4 shadow-sm border border-teal-100">
                                <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 font-bold mb-2">1</div>
                                <p class="text-sm text-slate-600">Desain Template & Variabel di sini.</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm border border-teal-100">
                                <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 font-bold mb-2">2</div>
                                <p class="text-sm text-slate-600">Buat SK baru, isi data, simpan sebagai <strong>Draft</strong>.</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm border border-teal-100">
                                <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center text-teal-600 font-bold mb-2">3</div>
                                <p class="text-sm text-slate-600">Review draft, edit jika perlu.</p>
                            </div>
                            <div class="bg-white rounded-lg p-4 shadow-sm border border-teal-100">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold mb-2">4</div>
                                <p class="text-sm text-slate-600">Klik <strong>Finalisasi</strong> untuk memberi Nomor Surat & mengunci arsip.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 1: Konsep Dasar -->
                    <div>
                        <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-bold text-sm">1</span>
                            Konsep Dasar
                        </h3>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200">
                            <p class="text-slate-600 mb-4">Template terdiri dari dua komponen utama yang saling terhubung:</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-lg p-4 border border-slate-200">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-code text-yellow-500"></i>
                                        <span class="font-bold text-slate-700">Form Config (Variabel)</span>
                                    </div>
                                    <p class="text-sm text-slate-500">Mendefinisikan input apa saja yang akan muncul saat membuat SK.</p>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-slate-200">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="fas fa-file-code text-green-500"></i>
                                        <span class="font-bold text-slate-700">HTML Pattern</span>
                                    </div>
                                    <p class="text-sm text-slate-500">Desain tampilan surat. Gunakan &#123;&#123;variable&#125;&#125; untuk data dinamis.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Tipe Field -->
                    <div>
                        <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                            <span class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 font-bold text-sm">2</span>
                            Tipe Field yang Tersedia
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg p-4 border border-slate-200">
                                <span class="font-bold text-green-600">text</span> - Input teks singkat
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200">
                                <span class="font-bold text-green-600">textarea</span> - Input teks panjang
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200">
                                <span class="font-bold text-green-600">date</span> - Pemilih tanggal
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200">
                                <span class="font-bold text-green-600">number</span> - Input angka
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200">
                                <span class="font-bold text-green-600">richtext</span> - Editor teks kaya
                            </div>
                            <div class="bg-white rounded-lg p-4 border border-slate-200">
                                <span class="font-bold text-green-600">select</span> - Dropdown pilihan
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- Modal Footer -->
                <div class="px-8 py-4 border-t border-slate-200 bg-slate-50 flex justify-end">
                    <button @click="showHelp = false" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-medium transition">
                        Tutup Panduan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Template Builder Container - OUTSIDE Vue template-app -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div id="template-builder-app" class="h-[700px]"></div>
    </div>
</div>

<!-- Template Builder Logic (runs first, mounts to #template-builder-app) -->
<script src="<?php echo base_url('assets/js/template_builder_vue.js?v=' . time()); ?>"></script>

<!-- Vue Logic for Form (mounts to #template-app) -->
<script>
(function() {
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            const form = ref({
                nama_sk: '',
                kategori: '',
                nomor_pattern: ''
            });
            
            const categories = ref(CATEGORIES || []);
            const showHelp = ref(false);

            const saveTemplate = () => {
                // Validate
                if (!form.value.nama_sk.trim()) {
                    alert('Nama template harus diisi!');
                    return;
                }
                if (!form.value.kategori) {
                    alert('Kategori harus dipilih!');
                    return;
                }

                // Get data from Template Builder
                const builderData = window.TemplateBuilderExport || {};

                // Populate hidden form
                document.getElementById('input_nama_sk').value = form.value.nama_sk;
                document.getElementById('input_kategori').value = form.value.kategori;
                document.getElementById('input_nomor_pattern').value = form.value.nomor_pattern;
                document.getElementById('form_config').value = builderData.form_config || '[]';
                document.getElementById('html_pattern').value = builderData.html_pattern || '';

                // Submit
                document.getElementById('template-form').submit();
            };

            return {
                form,
                categories,
                showHelp,
                saveTemplate
            };
        }
    }).mount('#template-app');
})();
</script>
