<x-guest-layout>
    <div class="fixed inset-0 w-screen h-screen bg-[#f5efe6] flex items-center justify-center">

        <div class="bg-white p-10 rounded-2xl shadow-md w-full max-w-md text-center">

            <!-- Icon -->
            <div class="flex justify-center mb-4">
                <div class="bg-orange-500 p-4 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M5 6h14M7 14h10m-9 4h8"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl font-bold text-gray-800">Welcome Back</h1>
            <p class="text-gray-500 mb-6">Sign in to your bakeshop account</p>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="text-left mb-4">
                    <label class="text-sm text-gray-700">Email Address</label>
                    <input type="email" name="email"
                        class="w-full mt-1 p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-400 outline-none"
                        placeholder="you@bakeshop.com" required>
                </div>

                <!-- Password -->
                <div class="text-left mb-6">
                    <label class="text-sm text-gray-700">Password</label>
                    <input type="password" name="password"
                        class="w-full mt-1 p-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-400 outline-none"
                        placeholder="••••••••" required>
                </div>

                <!-- Button -->
                <button class="w-full bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600 transition">
                    Sign In
                </button>

                <!-- Link -->
                <p class="mt-4 text-sm text-gray-500">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-orange-500 font-semibold">Create one</a>
                </p>
            </form>

        </div>

    </div>
</x-guest-layout>