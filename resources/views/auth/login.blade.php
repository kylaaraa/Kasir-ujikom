@extends('tailwind.index')

@section('content')
<div 
    class="flex items-center justify-center min-h-screen bg-cover bg-center" 
    style="background-image: url('https://i.pinimg.com/474x/4f/d7/ce/4fd7ce13e677485ec2849de64473c258.jpg'); background-color: rgb(227, 116, 239);"
>
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h2 class="mt-4 text-3xl font-bold text-gray-800">Login</h2>
        </div>

        <!-- Form login -->
        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        required
                    >
                </div>
            </div>

            <div class="flex justify-center mt-4">
                <button
                type="submit"
                class="w-32 py-2 bg-purple-600 text-white rounded-md shadow hover:bg-purple-700 transition focus:outline-none"
            >
                Login
            </button>
            
            </div>
        </form>
    </div>
</div>
@endsection
