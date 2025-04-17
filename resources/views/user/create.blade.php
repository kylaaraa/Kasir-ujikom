{{-- ALERT ERROR --}}
@if ($errors->any())
    <div id="errorAlert" class="bg-red-100 text-red-800 p-3 rounded mb-4 transition-opacity duration-1000">
        <ul class="list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>

    <script>
        setTimeout(() => {
            const errorBox = document.getElementById('errorAlert');
            if (errorBox) {
                errorBox.classList.add('opacity-0');
                setTimeout(() => errorBox.style.display = 'none', 1000);
            }
        }, 5000);
    </script>
@endif

{{-- MODAL FORM TAMBAH USER --}}
<div id="formCreate" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-full max-w-3xl p-8 rounded-xl shadow-lg relative">
        <button onclick="closeForm()" class="absolute top-4 right-4 text-gray-500 hover:text-red-500 text-xl">&times;</button>

        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tambah User</h2>

        <form action="{{ route('user.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama</label>
                <input type="text" id="name" name="name"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                    required>
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                    required>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                    required>
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                <select id="role" name="role"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                    required>
                    <option value="" disabled selected>Pilih Role</option>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>

            <div class="col-span-1 md:col-span-2 mt-2">
                <button type="submit"
                    class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition duration-200">
                    Tambah User
                </button>
            </div>
        </form>
    </div>
</div>

{{-- JS Untuk Buka/Tutup Modal --}}
<script>
    function openForm() {
        document.getElementById('formCreate').classList.remove('hidden');
    }

    function closeForm() {
        document.getElementById('formCreate').classList.add('hidden');
    }
</script>
