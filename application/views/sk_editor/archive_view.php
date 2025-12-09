<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Drafts</title>
    
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
    <!-- Vue 3 (CDN) -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <script>
        var ARCHIVES_DATA = <?php echo json_encode($archives); ?>;
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
            <a :href="dashboardUrl()" class="text-gray-400 hover:text-indigo-600 dark:hover:text-white mr-4 transition" title="Back to Dashboard">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white transition-colors duration-200">Saved Drafts</h1>
                <p class="text-slate-500 dark:text-gray-400 text-sm">Manage your generated SKs</p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
             <!-- Theme Toggle -->
            <button @click="toggleTheme" class="w-10 h-10 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-yellow-400 shadow-sm transition flex items-center justify-center" title="Toggle Theme">
                <i class="fas" :class="isDarkMode ? 'fa-sun' : 'fa-moon'"></i>
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-6 relative max-w-md">
        <span class="absolute inset-y-0 left-0 flex items-center pl-4">
            <i class="fas fa-search text-gray-400"></i>
        </span>
        <input type="text" v-model="searchQuery" placeholder="Search drafts..." 
            class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-slate-900 dark:text-white rounded-full pl-11 pr-6 py-2.5 shadow-sm focus:border-indigo-500 dark:focus:border-blue-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-blue-900 focus:outline-none transition-all duration-200">
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-200">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                    <th class="p-4">No. Surat</th>
                    <th class="p-4">Template</th>
                    <th class="p-4">Created At</th>
                    <th class="p-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <tr v-for="a in filteredArchives" :key="a.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150">
                    <td class="p-4 font-medium text-slate-900 dark:text-white">{{ a.no_surat }}</td>
                    <td class="p-4 text-slate-600 dark:text-gray-300">{{ a.nama_sk }}</td>
                    <td class="p-4 text-slate-500 dark:text-gray-400 text-sm font-mono">{{ a.created_at }}</td>
                    <td class="p-4 text-right space-x-2">
                        <a :href="editUrl(a.id)" class="text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition inline-block p-1" title="Edit Draft">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a :href="printUrl(a.id)" target="_blank" class="text-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400 transition inline-block p-1" title="Print PDF">
                            <i class="fas fa-print"></i>
                        </a>
                        <button @click="deleteDraft(a.id)" class="text-red-500 hover:text-red-600 dark:hover:text-red-400 transition inline-block p-1" title="Delete Draft">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <tr v-if="filteredArchives.length === 0">
                    <td colspan="4" class="p-12 text-center text-gray-500 dark:text-gray-400">
                       <div class="mb-2"><i class="fas fa-folder-open text-2xl text-gray-300 dark:text-gray-600"></i></div>
                        No drafts found.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

    <!-- Vue App Logic -->
    <script src="<?php echo base_url('assets/js/archive_manager_vue.js?v=' . time()); ?>"></script>
</body>
</html>
