<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Smart SK Generator</title>
    
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
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-pattern {
            background-color: #ffffff;
            background-image: radial-gradient(#2563eb 0.5px, transparent 0.5px), radial-gradient(#2563eb 0.5px, #ffffff 0.5px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            opacity: 0.05;
        }
    </style>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center relative overflow-hidden">
    
    <!-- Abstract Background Elements -->
    <div class="absolute inset-0 bg-pattern z-0 pointer-events-none"></div>
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-100 blur-3xl opacity-50 z-0"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-indigo-100 blur-3xl opacity-50 z-0"></div>

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative z-10 m-4">
        
        <!-- Left Side: Branding (Hidden on small screens) -->
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-800 p-12 flex-col justify-between relative text-white">
            <div class="relative z-10">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="bg-white/20 p-2 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-file-signature text-3xl"></i>
                    </div>
                    <span class="text-2xl font-bold tracking-tight">Smart SK</span>
                </div>
                
                <h2 class="text-4xl font-bold mb-6 leading-tight">Automation <br>Excellence.</h2>
                <p class="text-blue-100 text-lg opacity-90 max-w-sm">
                    Generate official decrees and decisions swiftly with our intelligent document processing system.
                </p>
            </div>
            
            <div class="relative z-10 text-sm text-blue-200">
                &copy; <?php echo date('Y'); ?> Pengadilan Agama Gorontalo
            </div>

            <!-- Decor -->
            <div class="absolute bottom-0 right-0 opacity-10">
                <i class="fas fa-fingerprint text-[12rem] -mb-12 -mr-12"></i>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 p-10 md:p-14 flex flex-col justify-center bg-white">
            
            <div class="mb-10 text-center md:text-left">
                <h3 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h3>
                <p class="text-gray-500">Please enter your credentials to access the dashboard.</p>
            </div>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3"></i>
                    <div>
                        <p class="text-sm text-red-700 font-medium">Login Failed</p>
                        <p class="text-xs text-red-600"><?= $this->session->flashdata('error') ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('auth/login') ?>" method="post" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text" name="username" id="username" required 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition sm:text-sm placeholder-gray-400" 
                            placeholder="Enter your username">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition sm:text-sm placeholder-gray-400" 
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>
                    <div class="text-sm">
                        <!-- <a href="#" class="font-medium text-blue-600 hover:text-blue-500">Forgot password?</a> -->
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all transform hover:-translate-y-0.5">
                        Sign in <i class="fas fa-arrow-right ml-2 mt-1"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-xs text-gray-400">
                Secure System &bull; Authorized Personnel Only
            </div>
        </div>
    </div>
</body>
</html>