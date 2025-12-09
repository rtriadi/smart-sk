<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart SK Dashboard</title>
    
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
        var SITE_URL = '<?php echo rtrim(site_url(), "/") . "/"; ?>';
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
            <div class="bg-indigo-600 dark:bg-blue-600 p-2 rounded-lg shadow-lg mr-4 transition-colors duration-200">
                <i class="fas fa-file-signature text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white transition-colors duration-200">Smart SK Generator</h1>
                <p class="text-slate-500 dark:text-gray-400 text-sm">Create official decrees efficiently</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
             <!-- Theme Toggle -->
            <button @click="toggleTheme" class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-yellow-400 shadow-sm transition flex items-center justify-center mr-2" title="Toggle Theme">
                <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>

            <a href="<?php echo site_url('templates'); ?>" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-slate-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 px-4 py-2 rounded shadow-sm transition flex items-center font-medium">
                <i class="fas fa-layer-group mr-2"></i> Templates
            </a>
            <a :href="manageUrl()" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-slate-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600 px-4 py-2 rounded shadow-sm transition flex items-center font-medium">
                <i class="fas fa-cog mr-2"></i> Settings
            </a>
            <a href="<?php echo site_url('sk_editor/archives'); ?>" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-green-700 dark:hover:bg-green-600 text-white px-4 py-2 rounded shadow-md transition flex items-center font-medium">
                <i class="fas fa-archive mr-2"></i> Saved Drafts
            </a>
            <a href="<?php echo site_url('auth/logout'); ?>" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow-md transition flex items-center font-medium" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-8 relative max-w-lg mx-auto">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4">
            <i class="fas fa-search text-gray-400"></i>
        </span>
        <input type="text" v-model="searchQuery" placeholder="Search templates..." 
            class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white rounded-full pl-11 pr-6 py-3 shadow-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-blue-900 focus:outline-none transition-all duration-200">
    </div>

    <!-- Grid -->
    <div v-if="filteredTemplates.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="t in filteredTemplates" :key="t.id" class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-xl border border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-blue-500 transition-all duration-300 flex flex-col transform hover:-translate-y-1">
            <div class="p-6 flex-1">
                <div class="flex justify-between items-start mb-4">
                    <div class="bg-indigo-50 dark:bg-blue-900/30 text-indigo-600 dark:text-blue-400 text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wide border border-indigo-100 dark:border-blue-800/50">
                        {{ t.kategori }}
                    </div>
                    <i class="fas fa-file-alt text-gray-300 dark:text-gray-600 text-2xl group-hover:text-indigo-500 dark:group-hover:text-blue-500 transition-colors"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2 leading-tight group-hover:text-indigo-700 dark:group-hover:text-blue-400 transition-colors">{{ t.nama_sk }}</h3>
                <p class="text-slate-500 dark:text-gray-400 text-xs font-mono bg-gray-50 dark:bg-gray-900 p-2 rounded border border-gray-100 dark:border-gray-700 truncate">
                    <i class="fas fa-hashtag mr-1 text-gray-300"></i> {{ t.nomor_pattern }}
                </p>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700 rounded-b-xl">
                <a :href="createUrl(t.id)" class="block w-full bg-white dark:bg-blue-600 hover:bg-indigo-600 dark:hover:bg-blue-500 text-indigo-600 group-hover:text-white dark:text-white text-center font-bold py-2.5 rounded-lg border border-indigo-200 dark:border-transparent group-hover:border-indigo-600 transition-all shadow-sm group-hover:shadow-md">
                    <i class="fas fa-plus mr-2"></i> Create SK
                </a>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-16">
        <div class="inline-block p-6 rounded-full bg-gray-100 dark:bg-gray-800 mb-6 animate-pulse">
            <i class="fas fa-search text-gray-400 dark:text-gray-500 text-4xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">No templates found</h3>
        <p class="text-slate-500 dark:text-gray-400">Try adjusting your search query.</p>
    </div>

</div>

<!-- Vue Logic -->
<script src="<?php echo base_url('assets/js/dashboard_vue.js'); ?>"></script>
</body>
</html>
