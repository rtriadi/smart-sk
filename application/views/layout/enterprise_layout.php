<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart SK - Enterprise</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                        },
                        teal: {
                            500: '#14b8a6',
                            600: '#0d9488',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-navy-900 text-white transition-transform duration-300 z-50 flex flex-col">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 bg-navy-900 border-b border-slate-700/50 shrink-0">
            <i class="fa-solid fa-file-signature text-teal-500 text-xl mr-3"></i>
            <span class="text-xl font-bold tracking-wide">Smart SK</span>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <button id="btnBuatSK" class="w-full bg-teal-500 hover:bg-teal-600 text-white font-bold py-2 px-4 rounded mb-6 flex items-center justify-center gap-2 shadow-lg hover:shadow-teal-500/30 transition-all">
                <i class="fa-solid fa-plus"></i>
                <span>Buat SK Baru</span>
            </button>

            <?php 
            $ci =& get_instance();
            $segment = $ci->uri->segment(2); // e.g. sk_editor/archives -> archives
            
            // Helper to determine active class
            function isActive($current, $target) {
                // Active State: bg-navy-800 text-white border-r-4 border-teal-500
                // Inactive State: text-slate-400 hover:bg-navy-800 hover:text-white transition-colors duration-200
                
                $activeClass = 'bg-navy-800 text-white border-r-4 border-teal-500';
                $inactiveClass = 'text-slate-400 hover:bg-navy-800 hover:text-white transition-colors duration-200 border-r-4 border-transparent';

                if ($current == $target) return $activeClass;
                if ($current == '' && $target == 'dashboard') return $activeClass;
                
                return $inactiveClass;
            }
            ?>

            <a href="<?= site_url('sk_editor') ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-none rounded-l-md transition-all <?= isActive($segment, 'dashboard') ?>">
                <i class="fa-solid fa-chart-line w-6 opacity-75 group-hover:opacity-100 transition-opacity"></i>
                Dashboard
            </a>

            <a href="<?= site_url('sk_editor/create') ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-none rounded-l-md transition-all <?= isActive($segment, 'create') ?>">
                <i class="fa-solid fa-plus-circle w-6 opacity-75 group-hover:opacity-100 transition-opacity"></i>
                Create New
            </a>

            <a href="<?= site_url('sk_editor/archives') ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-none rounded-l-md transition-all <?= isActive($segment, 'archives') ?>">
                <i class="fa-solid fa-folder-open w-6 opacity-75 group-hover:opacity-100 transition-opacity"></i>
                Archives
            </a>

            <a href="<?= site_url('sk_editor/settings') ?>" class="group flex items-center px-4 py-3 text-sm font-medium rounded-r-none rounded-l-md transition-all <?= isActive($segment, 'settings') ?>">
                <i class="fa-solid fa-cog w-6 opacity-75 group-hover:opacity-100 transition-opacity"></i>
                Settings
            </a>
        </nav>
        
        <!-- Sidebar Footer (Optional empty space or copyright) -->
        <div class="p-4 text-xs text-slate-500 text-center border-t border-slate-800/50">
            &copy; <?= date('Y') ?> Smart SK
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-40 flex items-center justify-between px-8 shadow-sm">
            <!-- Breadcrumbs / Page Title -->
            <div class="text-sm text-slate-500">
                <span class="font-medium text-slate-800">Enterprise Edition</span>
                <span class="mx-2 text-slate-300">/</span>
                <span class="capitalize font-semibold text-teal-600"><?= $segment ?: 'Dashboard' ?></span>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center space-x-2">
                <!-- Notifications -->
                <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors relative">
                    <i class="fa-regular fa-bell"></i>
                    <!-- Notification Dot -->
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>
                
                <div class="h-6 w-px bg-slate-200 mx-2"></div>
                
                <!-- Date -->
                 <span class="text-sm text-slate-500 font-medium hidden sm:block mr-2">
                    <?= date('d M Y') ?>
                 </span>

                <!-- User Profile (Polished) -->
                <div class="flex items-center pl-4 cursor-pointer group p-1.5 rounded-lg hover:bg-slate-50 transition-colors border-l border-slate-100">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-teal-500 to-teal-400 flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-white group-hover:ring-teal-100 transition-all">
                        <?= substr($this->session->userdata('nama') ?: 'A', 0, 1) ?>
                    </div>
                    <div class="ml-3 text-left">
                        <p class="text-sm font-semibold text-slate-700 group-hover:text-teal-700 transition-colors leading-tight">
                            <?= $this->session->userdata('nama') ?: 'Admin' ?>
                        </p>
                        <a href="<?= site_url('auth/logout') ?>" class="text-[10px] text-slate-400 hover:text-red-500 uppercase tracking-wider font-bold mt-0.5 inline-block">
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-8 bg-slate-50/50">
            <?= isset($page_content) ? $page_content : '' ?>
        </main>
        
    </div>

    <!-- Template Modal -->
    <div id="templateModal" class="relative z-[60] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-teal-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fa-solid fa-file-lines text-teal-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Pilih Template SK</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 mb-4">Silakan pilih template surat keputusan yang ingin Anda buat.</p>
                                    <select id="templateSelect" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6">
                                        <option value="">Memuat template...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" id="btnLanjut" class="inline-flex w-full justify-center rounded-md bg-teal-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-500 sm:ml-3 sm:w-auto">Lanjut</button>
                        <button type="button" id="btnCancel" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnOpen = document.getElementById('btnBuatSK');
            const modal = document.getElementById('templateModal');
            const btnCancel = document.getElementById('btnCancel');
            const btnLanjut = document.getElementById('btnLanjut');
            const select = document.getElementById('templateSelect');
            
            // Open Modal
            if(btnOpen) {
                btnOpen.addEventListener('click', (e) => {
                    e.preventDefault();
                    modal.classList.remove('hidden');
                    loadTemplates();
                });
            }
            
            // Close Modal
            if(btnCancel) {
                btnCancel.addEventListener('click', () => {
                    modal.classList.add('hidden');
                });
            }
            
            // Load Templates
            async function loadTemplates() {
                select.innerHTML = '<option>Loading...</option>';
                try {
                    const res = await fetch('<?= site_url('sk_editor/api_get_templates') ?>');
                    const data = await res.json();
                    
                    select.innerHTML = '';
                    if(data.length === 0) {
                        select.innerHTML = '<option value="">Tidak ada template tersedia</option>';
                        return;
                    }
                    
                    data.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.id;
                        opt.textContent = t.name;
                        select.appendChild(opt);
                    });
                } catch(e) {
                    console.error(e);
                    select.innerHTML = '<option>Error loading templates</option>';
                }
            }
            
            // Lanjut
            if(btnLanjut) {
                btnLanjut.addEventListener('click', () => {
                    const val = select.value;
                    if(val) {
                        window.location.href = '<?= site_url('sk_editor/create/') ?>' + val;
                    } else {
                        alert('Silakan pilih template terlebih dahulu');
                    }
                });
            }
        });
    </script>
</body>
</html>
