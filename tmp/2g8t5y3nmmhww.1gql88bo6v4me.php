<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LMS</title>
    <script src="<?= ($BASE) ?>/public/js/library/TailWind-3.4.17"></script>
    <link href="<?= ($BASE) ?>/public/css/fonts/css2.css" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="h-screen flex items-center justify-center bg-zinc-950 text-zinc-100">

    <div class="w-full max-w-md p-8">
        <!-- Minimalist Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-xl bg-zinc-900 border border-zinc-800 mb-4 text-zinc-100 font-bold text-xl">
                L
            </div>
            <h1 class="text-3xl font-semibold tracking-tight text-white">Welcome back</h1>
            <p class="text-zinc-500 text-sm mt-2">Enter your credentials to access the LMS</p>
        </div>

        <!-- Card Container -->
        <div class="bg-zinc-900/50 backdrop-blur-sm border border-zinc-800/50 rounded-2xl p-8 shadow-xl">
            <form id="loginForm" class="space-y-5">
                <div class="space-y-1">
                    <label class="block text-xs font-medium text-zinc-400 uppercase tracking-wider">Username</label>
                    <input type="text" name="username" 
                        class="w-full bg-zinc-950 border border-zinc-800 rounded-lg px-4 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-500 transition-all duration-200" 
                        placeholder="Enter your username" required>
                </div>
                
                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label class="block text-xs font-medium text-zinc-400 uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="password"
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-lg pl-4 pr-10 py-3 text-white placeholder-zinc-600 focus:outline-none focus:border-zinc-500 focus:ring-1 focus:ring-zinc-500 transition-all duration-200" 
                            placeholder="••••••••" required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center px-3 text-zinc-500 hover:text-zinc-300 transition-colors focus:outline-none">
                            <!-- Default Eye Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2 cursor-pointer group">
                        <input type="checkbox" id="rememberMe" class="w-3.5 h-3.5 rounded border-zinc-700 bg-zinc-900/50 text-white focus:ring-0 focus:ring-offset-0 accent-zinc-500">
                        <span class="text-xs text-zinc-500 group-hover:text-zinc-400 transition-colors">Remember me</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full bg-white hover:bg-zinc-200 text-zinc-900 font-medium py-3 rounded-lg transition-colors duration-200 shadow-[0_0_15px_rgba(255,255,255,0.1)]">
                        Sign In
                    </button>
                </div>

                <div id="msg" class="text-center text-red-400 text-xs font-medium h-4"></div>
            </form>
        </div>

        <div class="text-center mt-8">
            <p class="text-zinc-500 text-sm">
                Don't have an account? 
                <a href="<?= ($BASE) ?>/register" class="text-white hover:text-zinc-300 font-medium transition-colors">Create one</a>
            </p>
        </div>
    </div>

    <script src="<?= ($BASE) ?>/public/js/library/jquery-3.6.0.min.js"></script>
    <script src="<?= ($BASE) ?>/public/js/login.js"></script>
</body>
</html>