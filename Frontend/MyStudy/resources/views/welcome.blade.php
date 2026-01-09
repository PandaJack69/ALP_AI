<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MyStudy | AI Productivity Recommender</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Custom Styles -->
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                font-family: 'Instrument Sans', sans-serif;
            }
            .glass-effect {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            .text-gradient {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <!-- Main Card -->
            <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-center">
                    <h1 class="text-3xl font-bold text-white mb-2">MyStudy</h1>
                    <p class="text-indigo-100">AI-Powered Study Schedule Optimizer</p>
                </div>
                
                <!-- Content Section -->
                <div class="p-8 bg-white/95">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.168 0.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332 0.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332 0.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome to MyStudy</h2>
                        <p class="text-gray-600">Optimize your study schedule with AI recommendations based on your sleep patterns and daily activities.</p>
                    </div>
                    
                    <!-- Features List -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">AI-powered study hour recommendations</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">Sleep pattern analysis</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <span class="text-gray-700">Personalized productivity insights</span>
                        </div>
                    </div>
                    
                    <!-- Authentication Buttons -->
                    <div class="space-y-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="block w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-200 text-center">
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition duration-200 text-center">
                                    Log In
                                </a>
                                
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="block w-full bg-white text-indigo-600 font-semibold py-3 px-4 rounded-lg border-2 border-indigo-600 hover:bg-indigo-50 transition duration-200 text-center">
                                        Create Account
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                    
                    <!-- Demo Info -->
                    <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-500">
                            Get personalized study recommendations based on your sleep data and schedule
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-gray-300 text-sm">
                    &copy; {{ date('Y') }} MyStudy AI. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>