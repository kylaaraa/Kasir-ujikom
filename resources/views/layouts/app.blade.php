<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laravel Dashboard')</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    
    @stack('head')
</head>
<body class="bg-gray-100">

    <!-- Sidebar -->
    <div class="w-64 h-screen fixed top-0 left-0 bg-white text-purple-800 shadow-md border-r border-gray-200 px-4 py-6">
        <div class="flex items-center gap-3 mb-8">
            <span class="text-2xl font-bold tracking-wide">Kasir-App</span>
        </div>

        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-md transition
                          {{ Request::routeIs('dashboard') ? 'bg-purple-100 text-purple-900 font-semibold' : 'hover:bg-purple-50' }}">
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('products.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-md transition
                          {{ Request::routeIs('products.index') ? 'bg-purple-100 text-purple-900 font-semibold' : 'hover:bg-purple-50' }}">
                    <span>Produk</span>
                </a>
            </li>
            <li>
                <a href="{{ route('purchases.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-md transition
                          {{ Request::routeIs('purchases.index') ? 'bg-purple-100 text-purple-900 font-semibold' : 'hover:bg-purple-50' }}">
                    <span>Pembelian</span>
                </a>
            </li>
            <li>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('user.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-md transition
                          {{ Request::routeIs('user.index') ? 'bg-purple-100 text-purple-900 font-semibold' : 'hover:bg-purple-50' }}">
                    <span>User</span>
                </a>
                @endif
            </li>
        </ul>

        <!-- Logout Form - di bawah menu -->
        <div class="absolute bottom-6 left-0 right-0 px-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 rounded-md transition hover:bg-purple-50 text-red-600 hover:text-red-800">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="ml-64 min-h-screen">
        <div class="p-6">
            <!-- Topbar -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
            </div>

            <!-- Page Content -->
            <div>
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
