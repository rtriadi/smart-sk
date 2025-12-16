<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Template</title>
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Vue 3 (CDN) -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Ace Editor (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.32.7/ace.min.js"></script>

    <script>
        var TEMPLATE_DATA = {}; // Empty for create
        var CATEGORIES = <?php echo json_encode($categories); ?>;
        var SITE_URL = '<?php echo site_url(); ?>';
        var IS_EDIT = false;
    </script>
    <style>
        [v-cloak] { display: none; }
        .ace-editor { height: 400px; border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen flex flex-col text-gray-100 font-sans">

<div id="app" v-cloak class="container mx-auto px-4 py-8 flex-1">
    
    <!-- Header -->
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b border-gray-700 pb-4">
        <div class="flex items-center mb-4 md:mb-0">
            <a :href="cancelUrl()" class="text-gray-400 hover:text-white mr-4 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-white">Create New Template</h1>
        </div>
        <div class="flex flex-wrap justify-center gap-2 w-full md:w-auto">
            <a :href="cancelUrl()" class="px-4 py-2 rounded text-gray-300 hover:bg-gray-800 transition text-center">
                <i class="fas fa-times md:hidden"></i> <span class="hidden md:inline">Cancel</span>
            </a>
            <button @click="showHelp = true" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded font-bold transition flex items-center">
                <i class="fas fa-question-circle md:mr-2"></i> <span class="hidden md:inline">Panduan</span>
            </button>
            <button @click="saveTemplate" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded font-bold transition flex items-center">
                <i class="fas fa-save md:mr-2"></i> <span class="hidden md:inline">Save Template</span>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Basic Info -->
        <div class="space-y-6">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700">
                <h3 class="text-lg font-bold text-blue-400 mb-4 uppercase tracking-wider">Basic Information</h3>
                
                <div class="mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">Nama SK</label>
                    <input type="text" v-model="form.nama_sk" class="w-full bg-gray-900 border border-gray-600 text-white rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">Kategori</label>
                    <select v-model="form.kategori" class="w-full bg-gray-900 border border-gray-600 text-white rounded px-3 py-2 focus:border-blue-500 outline-none transition">
                        <option value="" disabled>Select Category</option>
                        <option v-for="c in categories" :key="c.id" :value="c.category_name">{{ c.category_name }}</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-400 text-sm font-bold mb-2">Nomor Pattern</label>
                    <input type="text" v-model="form.nomor_pattern" placeholder="e.g. W26-A/SK.OT1.6/{bulan}/{tahun}" class="w-full bg-gray-900 border border-gray-600 text-white rounded px-3 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">
                    <p class="text-gray-500 text-xs mt-1">Use {bulan}, {tahun} for auto-replacement.</p>
                </div>
            </div>

            <!-- JSON Config Editor -->
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700 flex flex-col h-[500px]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-yellow-400 uppercase tracking-wider">Form Config (JSON)</h3>
                    <span class="text-xs text-gray-500 bg-gray-900 px-2 py-1 rounded">JSON Mode</span>
                </div>
                <div id="json-editor" class="ace-editor flex-1"></div>
            </div>
        </div>

        <!-- Right Column: HTML Editor -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg border border-gray-700 flex flex-col h-full min-h-[800px]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-green-400 uppercase tracking-wider">HTML Pattern</h3>
                    <span class="text-xs text-gray-500 bg-gray-900 px-2 py-1 rounded">HTML Mode</span>
                </div>
                <div id="html-editor" class="ace-editor flex-1"></div>
            </div>
        </div>
    </div>

    <!-- Help Modal -->
    <div v-if="showHelp" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4" @click.self="showHelp = false">
        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col border border-gray-700">
            <div class="flex justify-between items-center p-6 border-b border-gray-700">
                <h2 class="text-2xl font-bold text-white">Panduan Pembuatan Template SK</h2>
                <button @click="showHelp = false" class="text-gray-400 hover:text-white text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto space-y-8 text-gray-300" v-pre>
                
                <!-- Section 1: Konsep Dasar -->
                <div>
                    <h3 class="text-xl font-bold text-blue-400 mb-2">1. Konsep Dasar</h3>
                    <p class="mb-2">Pembuatan template terdiri dari dua bagian yang saling terhubung:</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li><strong>Form Config (JSON):</strong> Mendefinisikan inputan apa saja yang akan muncul di form pengisian (sebelah kiri editor). Setiap field memiliki <code>variable</code>.</li>
                        <li><strong>HTML Pattern:</strong> Desain tampilan surat. Gunakan <code>{{variable}}</code> untuk menampilkan data dari form ke dalam surat.</li>
                    </ul>
                </div>

                <!-- Section 2: JSON Config -->
                <div>
                    <h3 class="text-xl font-bold text-yellow-400 mb-2">2. Struktur JSON Config</h3>
                    <p class="mb-2">Format JSON adalah array dari "Section". Contoh sederhana:</p>
                    <pre class="bg-gray-900 p-3 rounded text-sm font-mono border border-gray-700">
[
  {
    "title": "DATA UTAMA",
    "fields": [
      {
        "type": "text",
        "label": "Nomor Surat",
        "variable": "no_sk",
        "default": "..."
      },
      {
         "type": "date",
         "label": "Tanggal",
         "variable": "tanggal_sk"
      }
    ]
  }
]</pre>
                    <div class="mt-4">
                        <h4 class="font-bold text-white mb-2">Tipe Field yang Tersedia:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-900 p-3 rounded border border-gray-700">
                                <span class="text-green-400 font-bold">text</span>
                                <p class="text-xs">Input teks singkat (satu baris).</p>
                            </div>
                            <div class="bg-gray-900 p-3 rounded border border-gray-700">
                                <span class="text-green-400 font-bold">textarea</span>
                                <p class="text-xs">Input teks panjang (paragraf).</p>
                            </div>
                            <div class="bg-gray-900 p-3 rounded border border-gray-700">
                                <span class="text-green-400 font-bold">date</span>
                                <p class="text-xs">Pemilih tanggal. Otomatis menghasilkan <code>{{tanggal_indo}}</code> & <code>{{tanggal_hijri}}</code>.</p>
                            </div>
                            <div class="bg-gray-900 p-3 rounded border border-gray-700">
                                <span class="text-green-400 font-bold">select</span>
                                <p class="text-xs">Dropdown pilihan. Perlu properti <code>options: ["A", "B"]</code>.</p>
                            </div>
                            <div class="bg-gray-900 p-3 rounded border border-gray-700">
                                <span class="text-green-400 font-bold">checkbox</span>
                                <p class="text-xs">Switch on/off. Menghasilkan nilai true/false. Berguna untuk logika <code>{{#if}}</code>.</p>
                            </div>
                            <div class="bg-gray-900 p-3 rounded border border-gray-700">
                                <span class="text-green-400 font-bold">repeater</span>
                                <p class="text-xs">Daftar dinamis (Tambah/Hapus item). Contoh: Menimbang, Mengingat.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: HTML Pattern -->
                <div>
                    <h3 class="text-xl font-bold text-green-400 mb-2">3. HTML Pattern & Variabel</h3>
                    <p class="mb-4">Gunakan HTML standar dengan syntax Handlebars untuk menyisipkan data.</p>
                    
                    <h4 class="font-bold text-white mb-2">Variabel Sistem (Otomatis Tersedia)</h4>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-400 border border-gray-700">
                            <thead class="text-xs text-gray-200 uppercase bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2">Variabel</th>
                                    <th class="px-4 py-2">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-gray-800 border-b border-gray-700">
                                    <td class="px-4 py-2 font-mono text-blue-300">{{tanggal_indo}}</td>
                                    <td class="px-4 py-2">Tanggal format Indonesia (contoh: 12 Desember 2024). Muncul otomatis jika ada input <code>type: "date"</code>.</td>
                                </tr>
                                <tr class="bg-gray-800 border-b border-gray-700">
                                    <td class="px-4 py-2 font-mono text-blue-300">{{tanggal_hijri}}</td>
                                    <td class="px-4 py-2">Tanggal format Hijriah. Muncul otomatis mendampingi tanggal masehi.</td>
                                </tr>
                                <tr class="bg-gray-800 border-b border-gray-700">
                                    <td class="px-4 py-2 font-mono text-purple-300">{{globalSettings.kopTitle1}}</td>
                                    <td class="px-4 py-2">Judul Kop Surat Baris 1 (dari Global Settings). Tersedia Title1 s.d Title4.</td>
                                </tr>
                                <tr class="bg-gray-800 border-b border-gray-700">
                                    <td class="px-4 py-2 font-mono text-purple-300">{{globalSettings.kopAddress}}</td>
                                    <td class="px-4 py-2">Alamat Kantor (dari Global Settings).</td>
                                </tr>
                                <tr class="bg-gray-800 border-b border-gray-700">
                                    <td class="px-4 py-2 font-mono text-purple-300">{{globalSettings.kopLogo}}</td>
                                    <td class="px-4 py-2">URL Logo Instansi. Gunakan di dalam tag img src.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4 class="font-bold text-white mt-4 mb-2">Syntax Logika</h4>
                    <div class="space-y-4">
                        <div class="bg-gray-900 p-4 rounded border border-gray-700">
                            <p class="font-bold text-gray-300 mb-1">Mencetak Variabel:</p>
                            <code class="text-blue-300">{{variabel_anda}}</code>
                        </div>
                        <div class="bg-gray-900 p-4 rounded border border-gray-700">
                            <p class="font-bold text-gray-300 mb-1">Looping (Untuk Repeater):</p>
                            <pre class="text-green-300 text-sm">
&lt;ol&gt;
  {{#each list_menimbang}}
    &lt;li&gt;{{this}}&lt;/li&gt;
  {{/each}}
&lt;/ol&gt;</pre>
                        </div>
                        <div class="bg-gray-900 p-4 rounded border border-gray-700">
                            <p class="font-bold text-gray-300 mb-1">Kondisional (If):</p>
                            <pre class="text-yellow-300 text-sm">
{{#if tampilkan_salinan}}
  &lt;div&gt;Ini hanya muncul jika checkbox dicentang&lt;/div&gt;
{{/if}}</pre>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="p-6 border-t border-gray-700 flex justify-end">
                <button @click="showHelp = false" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded font-bold transition">
                    Tutup Panduan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="mt-auto border-t border-gray-700 pt-6 pb-6 text-center bg-gray-900">
    <p class="text-xs text-gray-400 font-medium">
        Developed by Rahmat Triadi, S.Kom. &copy; <?= date('Y') ?>
    </p>
</div>

<!-- Vue Logic -->
<script src="<?php echo base_url('assets/js/template_form_vue.js'); ?>"></script>
</body>
</html>
