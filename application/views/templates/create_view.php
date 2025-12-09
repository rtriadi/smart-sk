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
        var SITE_URL = '<?php echo site_url(); ?>';
        var IS_EDIT = false;
    </script>
    <style>
        [v-cloak] { display: none; }
        .ace-editor { height: 400px; border-radius: 0.5rem; }
    </style>
</head>
<body class="bg-gray-900 min-h-screen text-gray-100 font-sans">

<div id="app" v-cloak class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-700 pb-4">
        <div class="flex items-center">
            <a :href="cancelUrl()" class="text-gray-400 hover:text-white mr-4 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-white">Create New Template</h1>
        </div>
        <div class="flex gap-3">
            <a :href="cancelUrl()" class="px-4 py-2 rounded text-gray-300 hover:bg-gray-800 transition">Cancel</a>
            <button @click="saveTemplate" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-2 rounded font-bold transition flex items-center">
                <i class="fas fa-save mr-2"></i> Save Template
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
                        <option value="kepegawaian">Kepegawaian</option>
                        <option value="ortala">Ortala</option>
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

</div>

<!-- Vue Logic -->
<script src="<?php echo base_url('assets/js/template_form_vue.js'); ?>"></script>
</body>
</html>
