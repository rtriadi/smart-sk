<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Templates</title>
    
    <!-- Tailwind CSS (CDN) -->
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr & jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Vue 3 (CDN) -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script>
        var TEMPLATES_DATA = <?php echo json_encode($templates); ?>;
        var SITE_URL = '<?php echo site_url(); ?>';
    </script>
    <style>
        [v-cloak] { display: none; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen text-slate-900 dark:text-gray-100 font-sans transition-colors duration-200">

<div id="app" v-cloak class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-8 border-b border-gray-200 dark:border-gray-700 pb-4 transition-colors duration-200">
        <div class="flex items-center">
            <a :href="dashboardUrl()" class="text-gray-500 hover:text-indigo-600 dark:hover:text-white mr-4 transition flex items-center font-medium" title="Back to Dashboard">
                <i class="fas fa-arrow-left mr-2"></i> Templates
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white transition-colors duration-200">Template Manager</h1>
                <p class="text-slate-500 dark:text-gray-400 text-sm">Add, edit, or remove SK templates</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
             <!-- Theme Toggle -->
            <button @click="toggleTheme" class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-yellow-400 shadow-sm transition flex items-center justify-center mr-2" title="Toggle Theme">
                <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>
            <a href="<?php echo site_url('auth/logout'); ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow-md transition flex items-center font-medium mr-2" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>

            <a :href="createUrl()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow-md font-semibold transition flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Template
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 relative max-w-md">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4">
            <i class="fas fa-search text-gray-400"></i>
        </span>
        <input type="text" v-model="searchQuery" placeholder="Search templates..." 
            class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white rounded-full pl-11 pr-6 py-2.5 shadow-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-blue-900 focus:outline-none transition-all duration-200">
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-200">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <th class="p-4">ID</th>
                    <th class="p-4">Nama SK</th>
                    <th class="p-4">Kategori</th>
                    <th class="p-4">Pattern Nomor</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr v-for="t in filteredTemplates" :key="t.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                    <td class="p-4 text-gray-500 dark:text-gray-400 font-mono text-xs">#{{ t.id }}</td>
                    <td class="p-4 font-medium text-slate-900 dark:text-white">{{ t.nama_sk }}</td>
                    <td class="p-4">
                        <span class="bg-indigo-50 dark:bg-blue-900/30 text-indigo-600 dark:text-blue-400 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wide border border-indigo-100 dark:border-transparent">
                            {{ t.kategori }}
                        </span>
                    </td>
                    <td class="p-4 text-slate-600 dark:text-gray-300 font-mono text-sm bg-slate-50 dark:bg-transparent rounded">{{ t.nomor_pattern }}</td>
                    <td class="p-4 text-right space-x-2">
                        <a :href="useUrl(t.id)" class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition inline-block p-1" title="Use Template">
                            <i class="fas fa-play"></i>
                        </a>
                        <a :href="editUrl(t.id)" class="text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition inline-block p-1" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a :href="deleteUrl(t.id)" @click="confirmDelete" class="text-red-400 hover:text-red-600 dark:hover:text-red-300 transition inline-block p-1" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <tr v-if="filteredTemplates.length === 0">
                    <td colspan="5" class="p-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="mb-2"><i class="fas fa-search text-2xl text-gray-300 dark:text-gray-600"></i></div>
                        No templates found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<!-- Vue Logic -->
<script src="<?php echo base_url('assets/js/template_manager_vue.js'); ?>"></script>
</body>
</html>
