<!DOCTYPE html>
<html lang="en" x-data="{ showPassword: false, showStatus: true }" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gradient-to-tr from-indigo-100 to-white min-h-screen flex items-center justify-center px-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <h2 class="text-3xl font-bold mb-6 text-center text-gray-800">Interlink Portal Login</h2>
    <!-- Inclusive subtitle for all user types -->
    <!-- General marketing subtitle for all users -->
    <p class="mb-4 text-center text-gray-500">Sign in to connect, collaborate, and unlock new opportunities on Interlink.</p>

        <!-- Session Status -->
        @if (session('status'))
            <div
                x-show="showStatus"
                x-init="setTimeout(() => showStatus = false, 3000)"
                class="mb-4 text-sm font-medium text-green-600 bg-green-100 px-4 py-2 rounded-md border border-green-300"
            >
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <!-- Email Address -->
            <div>
                                <!--
                                    For organization staff, use your personal email (not company email).
                                    For students, use your student email.
                                    For admins, use your admin email.
                                -->
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                                <input id="email" name="email" type="email" autocomplete="username" required autofocus
                                             value="{{ old('email') }}"
                                             class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                             placeholder="Enter email address" />
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" id="password" name="password" required autocomplete="current-password"
                           class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none" />
                    <button type="button" @click="show = !show"
                            class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-600 hover:text-gray-800 focus:outline-none">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg x-show="show" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.96 9.96 0 012.223-3.592m3.122-2.36A9.959 9.959 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.247 5.036M15 12a3 3 0 00-3-3m0 0a3 3 0 00-3 3m6 0a3 3 0 01-3 3m0 0a3 3 0 01-3-3M3 3l18 18"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                    Remember me
                </label>
            </div>

            <div class="flex items-center justify-between pt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 hover:text-indigo-800" href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                @endif

                <!-- Register Button -->
                <a class="text-sm text-indigo-600 hover:text-indigo-800" href="{{ route('register') }}">
                    Register
                </a>
            </div>

                <button type="submit"
                        class="ml-3 inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Log in
                </button>

        </form>
    </div>
</body>
</html>
