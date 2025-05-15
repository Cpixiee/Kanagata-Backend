<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kanagata - Log In</title>
    <link rel="stylesheet" href="{{ asset('src/output.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/login.js') }}" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</head>

<body class="w-full max-h-screen overflow-hidden">
    <div class="flex">
        <div class="w-3/5 h-screen">
            <div class="px-24 py-12">
                <h1 class="font-poppins text-[32px] font-medium">Welcome to Kanagata</h1>
                <p class="font-poppins text-[16px]">To access the dashboard you need to Log In.</p>
                
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="py-4">
                        <h1 class="font-poppins text-[16px] text-[#9698AD]">Username</h1>
                        <input type="text" id="username" name="username"
                               class="w-full p-2 border border-[#9698AD] rounded-lg" required
                               value="{{ old('username') }}">
                        @error('username')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="py-4">
                        <h1 class="font-poppins text-[16px] text-[#9698AD]">Password</h1>
                        <input type="password" id="password" name="password"
                               class="w-full p-2 border border-[#9698AD] rounded-lg" required>
                        @error('password')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <div class="py-4">
                        <h1 class="font-poppins text-[16px]">By logging in, you agree to the
                            <a href="#terms" onclick="alert('Terms page will be added later')">
                                <span class="underline">Terms of use</span>
                            </a>
                            and
                            <a href="#privacy" onclick="alert('Privacy policy will be added later')">
                                <span class="underline">Privacy Policy.</span>
                            </a>
                        </h1>
                    </div>
                    
                    <div class="pt-6">
                        <button type="submit"
                                class="bg-[#C3C3C3] text-white hover:bg-[#5E5FEF] font-poppins text-[22px] py-4 px-12 rounded-full">
                            Log In
                        </button>
                    </div>
                </form>
                
                @if (session('error'))
                    <p class="font-poppins text-red-500 mt-4">{{ session('error') }}</p>
                @endif
            </div>
        </div>
        <div class="w-2/5 h-screen">
            <img class="w-full max-h-screen object-cover" src="{{ asset('img/login-img.png') }}" alt="Login Image">
        </div>
    </div>
</body>
</html>