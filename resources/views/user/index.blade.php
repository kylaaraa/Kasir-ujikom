@extends('layouts.app')

@section('title', 'User')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Data User</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4 text-base">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-4">
        <div class="flex-1"></div> <!-- Spacer untuk tombol "Tambah User" di sebelah kanan -->

        <button onclick="openForm()" class="border border-purple-600 text-purple-600 py-2 px-4 rounded-lg hover:bg-purple-600 hover:text-white transition duration-200 text-base">
            Tambah User
        </button>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded-lg">
        <table class="min-w-full table-auto text-base text-center border-collapse">
            <thead class="bg-purple-600 text-white">
                <tr>
                    <th class="px-4 py-3 text-center border-b">Email</th>
                    <th class="px-4 py-3 text-center border-b">Nama</th>
                    <th class="px-4 py-3 text-center border-b">Role</th>
                    @if(auth()->user()->role === 'admin')
                        <th class="px-4 py-3 text-center border-b">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="text-gray-700">
                @foreach($users as $user)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $user->email }}</td>
                    <td class="px-4 py-3">{{ $user->name }}</td>
                    <td class="px-4 py-3 capitalize">{{ $user->role }}</td>
                    <td class="px-4 py-3 text-center space-x-2">
                        <a href="{{ route('user.edit', $user->id) }}"
                           class="border border-yellow-500 text-yellow-500 px-3 py-1 rounded hover:bg-yellow-500 hover:text-white transition duration-200 text-sm">
                            Edit
                        </a>
                        <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                              class="inline-block" onsubmit="return confirm('Hapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="border border-red-500 text-red-500 px-3 py-1 rounded hover:bg-red-500 hover:text-white transition duration-200 text-sm">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@include('user.create')
@endsection
