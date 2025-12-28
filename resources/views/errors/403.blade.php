<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.5); }
            50% { box-shadow: 0 0 40px rgba(251, 191, 36, 0.8); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        .pulse-glow {
            animation: pulse-glow 2s ease-in-out infinite;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="antialiased bg-gradient-to-br from-amber-50 via-orange-50 to-amber-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full">
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-amber-400 via-orange-500 to-amber-600 px-8 py-6">
                <div class="flex items-center justify-center gap-3">
                    <img src="/images/logo.png" alt="A2 VET Logo" class="h-16 w-auto drop-shadow-lg">
                    <span class="text-4xl font-bold text-white drop-shadow-md">A2 VET</span>
                </div>
            </div>

            <div class="p-8 md:p-12">
                <div class="text-center">
                    <!-- 403 Icon with animation -->
                    <div class="flex justify-center mb-8">
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-400 to-orange-500 rounded-full blur-2xl opacity-50"></div>
                            <div class="relative bg-gradient-to-br from-amber-100 to-orange-100 rounded-full p-8 pulse-glow">
                                <svg class="w-24 h-24 text-amber-600 float-animation" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Error Code -->
                    <h1 class="text-8xl font-extrabold gradient-text mb-4">403</h1>
                    
                    <!-- Main Message -->
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                        Mohon Maaf
                    </h2>
                    
                    <!-- Error Description -->
                    <div class="max-w-2xl mx-auto">
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-l-4 border-amber-500 rounded-lg p-6 mb-8">
                            <p class="text-lg text-gray-700 leading-relaxed">
                                {{ $exception->getMessage() ?: 'Anda tidak memiliki akses ke halaman ini.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="grid md:grid-cols-2 gap-4 max-w-2xl mx-auto mb-8">
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-full p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-medium text-amber-600">Hubungi Kami</p>
                                    <p class="text-sm font-bold text-gray-800">Telepon</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-4 shadow-md hover:shadow-lg transition-shadow">
                            <div class="flex items-center gap-3">
                                <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-full p-3">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-medium text-amber-600">Email Kami</p>
                                    <p class="text-sm font-bold text-gray-800">Informasi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a 
                            href="{{ url('/') }}" 
                            class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl hover:from-amber-600 hover:to-orange-700 transition-all duration-300 transform hover:scale-105"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Kembali ke Beranda
                        </a>

                        <button 
                            onclick="window.history.back()" 
                            class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-amber-600 font-semibold rounded-xl shadow-lg hover:shadow-xl border-2 border-amber-500 hover:bg-amber-50 transition-all duration-300 transform hover:scale-105"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Halaman Sebelumnya
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-r from-amber-100 to-orange-100 px-8 py-4 text-center">
                <p class="text-sm text-gray-600">
                    &copy; {{ date('Y') }} A2 VET Klinik Hewan. Semua hak dilindungi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
