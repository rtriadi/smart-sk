<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Smart SK</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>
</head>
<body class="h-screen w-full flex flex-col md:flex-row bg-white overflow-hidden font-sans">

    <!-- Left Side (60%) -->
    <div class="hidden md:flex md:w-[60%] bg-navy-900 relative flex-col justify-center items-center text-white overflow-hidden">
        <!-- Abstract Background Elements -->
        <div class="absolute inset-0 opacity-5 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDQwIDQwIiBmaWxsPSIjZmZmZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDUiPjxwYXRoIGQ9Ik0wIDBoNDB2NDBIMHoiIGZpbGw9Im5vbmUiLz48Y2lyY2xlIGN4PSIyMCIgY3k9IjIwIiByPSIxIi8+PC9zdmc+')]"></div>
        <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 rounded-full bg-teal-500 blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-80 h-80 rounded-full bg-blue-600 blur-3xl opacity-20"></div>

        <div class="relative z-10 text-center p-12 max-w-2xl">
            <div class="inline-flex items-center justify-center p-6 bg-white/5 rounded-3xl backdrop-blur-sm mb-8 shadow-2xl border border-white/10 ring-1 ring-white/5">
                <i class="fas fa-file-signature text-6xl text-teal-400"></i>
            </div>
            <h1 class="text-5xl font-bold tracking-tight mb-4 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-300">Smart SK</h1>
            <p class="text-2xl text-gray-300 font-light tracking-wide leading-relaxed">Professional Document Management</p>
            <div class="mt-8 flex justify-center space-x-2">
                <div class="h-1 w-16 bg-teal-500 rounded-full"></div>
                <div class="h-1 w-4 bg-teal-500/50 rounded-full"></div>
            </div>
        </div>
        
        <div class="absolute bottom-10 text-gray-500 text-sm tracking-wider uppercase">
            &copy; <?= date('Y') ?> Pengadilan Agama Gorontalo
        </div>
    </div>

    <!-- Right Side (40%) -->
    <div class="w-full md:w-[40%] flex flex-col justify-center items-center p-8 md:p-12 bg-white h-full overflow-y-auto relative">
        <div class="w-full max-w-md space-y-8">
            
            <!-- Mobile Logo -->
            <div class="md:hidden text-center mb-8">
                <div class="inline-flex items-center justify-center p-4 bg-navy-900 rounded-2xl shadow-lg">
                    <i class="fas fa-file-signature text-3xl text-teal-400"></i>
                </div>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Smart SK</h2>
            </div>

            <div class="text-center md:text-left">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">Welcome Back</h2>
                <p class="mt-2 text-sm text-gray-500">Please sign in to access your dashboard</p>
            </div>

            <!-- Error Handling -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Login Failed</h3>
                            <div class="mt-1 text-sm text-red-700">
                                <p><?= $this->session->flashdata('error') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="<?= site_url('auth/login') ?>" method="POST">
                <div class="space-y-5">
                    <!-- Username -->
                    <div class="group">
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1 transition-colors group-focus-within:text-teal-600">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400 group-focus-within:text-teal-500 transition-colors"></i>
                            </div>
                            <input type="text" name="username" id="username" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-100 focus:border-teal-500 transition-all duration-200 sm:text-sm placeholder-gray-400 bg-gray-50 focus:bg-white" 
                                placeholder="Enter your username">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="group">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1 transition-colors group-focus-within:text-teal-600">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-teal-500 transition-colors"></i>
                            </div>
                            <input type="password" name="password" id="password" required 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-100 focus:border-teal-500 transition-all duration-200 sm:text-sm placeholder-gray-400 bg-gray-50 focus:bg-white" 
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded cursor-pointer">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none">Remember me</label>
                    </div>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 shadow-lg hover:shadow-teal-500/30 transform hover:-translate-y-0.5">
                        Sign in
                        <i class="fas fa-arrow-right ml-2 mt-0.5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
            
            <div class="pt-8 mt-8 border-t border-gray-100">
                <p class="text-center text-xs text-gray-400">
                    Developed by Rahmat Triadi, S.Kom. &copy; <?= date('Y') ?>
                </p>
            </div>
        </div>
    </div>

</body>
</html>