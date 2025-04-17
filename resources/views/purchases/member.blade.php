@extends('layouts.app')

@section('title', 'Pembelian')
@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white shadow rounded-xl">
    <h2 class="text-xl font-bold mb-4">Konfirmasi Pembelian</h2>

    {{-- Ringkasan Produk --}}
    <div class="mb-6">
        <table class="w-full border text-sm text-left">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-3 py-2">Produk</th>
                    <th class="border px-3 py-2">Qty</th>
                    <th class="border px-3 py-2">Harga</th>
                    <th class="border px-3 py-2">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data['products'] as $item)
                <tr>
                    <td class="border px-3 py-2">{{ $item['nama_produk'] }}</td>
                    <td class="border px-3 py-2">{{ $item['qty'] }}</td>
                    <td class="border px-3 py-2">Rp {{ number_format($item['harga'], 0, ',', '.') }}</td>
                    <td class="border px-3 py-2">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="bg-gray-100 font-semibold">
                    <td colspan="3" class="border px-3 py-2 text-right">Total</td>
                    <td class="border px-3 py-2" id="total-price-cell">Rp {{ number_format($data['total_price'], 0, ',', '.') }}</td>
                </tr>
                <tr class="text-sm text-red-600">
                    <td colspan="3" class="border px-3 py-2 text-right">Diskon Poin</td>
                    <td class="border px-3 py-2" id="diskon-poin-cell">Rp 0</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Form Pembayaran --}}
    <form id="purchaseForm" action="{{ route('purchases.finish') }}" method="POST">
        @csrf
        <input type="hidden" name="diskon_poin" id="diskon_poin" value="0">

        {{-- Pilih Member --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Apakah Pembeli Member?</label>
            <select name="is_member" id="is_member" class="w-full border rounded px-3 py-2">
                <option value="0">Bukan</option>
                <option value="1">Ya</option>
            </select>
        </div>

        {{-- Jika Member --}}
        <div id="member_fields" class="hidden">
            <div class="mb-4">
                <label class="block font-medium mb-1">Nomor Telepon</label>
                <input type="text" name="no_phone" id="no_phone" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Nama</label>
                <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <p id="member-points" class="text-sm text-gray-600 hidden">Poin Anda: <span id="points-amount">0</span></p>
            </div>
            <!-- Checkbox Gunakan Poin -->
            <div class="mb-4" id="point_section" style="display: none;">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="use_points_checkbox" value="1" class="mr-2" id="use_points">
                    <span>Gunakan poin untuk potongan</span>
                </label>
            </div>

            <p id="point_info" class="text-sm text-gray-500 italic hidden">Poin belum tersedia karena ini transaksi pertama.</p>
        </div>

        {{-- Total Bayar --}}
        <div class="mb-4">
            <label class="block font-medium mb-1">Total Pembayaran</label>
            <div class="flex items-center border rounded px-3 py-2">
                <span class="text-gray-600 mr-1">Rp</span>
                <input type="text" id="total_payment" name="total_payment" class="w-full outline-none" required>
            </div>
            <p id="reminder" class="text-sm text-red-500 mt-1 hidden">
                💡 Total pembayaran harus minimal Rp {{ number_format($data['total_price'], 0, ',', '.') }}
            </p>
        </div>

        {{-- Submit --}}
        <div class="text-center mt-6">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Selesaikan Pembelian
            </button>
        </div>
    </form>
</div>

{{-- Script --}}
<script>
    const totalHarga = {{ $data['total_price'] }};
    let memberPoints = 0;
    let diskonPoin = 0;

    const form = document.getElementById('purchaseForm'); //untuk menangani submit.
    const isMemberSelect = document.getElementById('is_member');//untuk memilih apakah pembeli member atau bukan.
    const phoneInput = document.getElementById('no_phone'); //tempat user mengisi nomor telepon.
    const nameInput = document.getElementById('name'); //tempat nama member akan otomatis terisi.
    const usePointsCheckbox = document.getElementById('use_points'); //untuk memilih apakah ingin menggunakan poin.
    const paymentInput = document.getElementById('total_payment'); // tempat user mengisi jumlah pembayaran.
    const reminder = document.getElementById('reminder'); //peringatan
    const memberFields = document.getElementById('member_fields'); //Container untuk input tambahan jika user adalah member.
    const memberPointsInfo = document.getElementById('member-points'); //info poin
    const pointsAmount = document.getElementById('points-amount'); //Tempat menampilkan jumlah poin member secara visual.
    const pointSection = document.getElementById('point_section'); //Bagian UI untuk menggunakan poin (biasanya berisi checkbox + info diskon).
    const pointInfo = document.getElementById('point_info'); //Elemen untuk menampilkan info jika poin tidak bisa digunakan (misalnya karena belum pernah belanja).
    const totalPriceCell = document.getElementById('total-price-cell'); //total harga
    const diskonPoinCell = document.getElementById('diskon-poin-cell'); //nilai diskon poin dalam bentuk uang
    const diskonPoinInput = document.getElementById('diskon_poin'); //Input tersembunyi (hidden) yang mengirim nilai diskon ke backend saat form disubmit.

    isMemberSelect.addEventListener('change', function () {
        if (this.value === '1') {
            memberFields.classList.remove('hidden');
            memberPointsInfo.classList.remove('hidden');
        } else {
            memberFields.classList.add('hidden');
            memberPointsInfo.classList.add('hidden');
            pointSection.style.display = 'none';
            pointInfo.classList.add('hidden');
        }
    });
    //Ketika pilihan di dropdown "Apakah Member?" berubah, script akan memeriksa apakah pengguna adalah member (value === '1').
    //Jika pengguna adalah member, kolom-kolom terkait member (memberFields, memberPointsInfo) akan ditampilkan.
    //Jika pengguna bukan member, kolom-kolom tersebut disembunyikan.

    phoneInput.addEventListener('blur', fetchMemberData);
    phoneInput.addEventListener('change', fetchMemberData);
    //Ketika pengguna mengisi atau mengubah nomor telepon dan fokus meninggalkan input, fungsi fetchMemberData() akan dipanggil.
    //Fungsi ini akan mengambil data member berdasarkan nomor telepon yang dimasukkan melalui endpoint API /check-member-history. Jika member ada, data seperti poin dan nama akan ditampilkan.

    function fetchMemberData() {
        const phone = phoneInput.value.trim();
        if (isMemberSelect.value !== '1') return;

        if (phone.length > 0) {
            fetch(`/check-member-history?no_phone=${phone}`)
                .then(res => res.json())
                .then(data => {
                    if (data.exists) {
                        memberPoints = data.points;
                        pointsAmount.innerText = memberPoints;
                        nameInput.value = data.name;

                        //Jika member sudah melakukan pembelian sebelumnya (hasPurchases)
                        if (data.hasPurchases) {
                            pointSection.style.display = 'block';
                            pointInfo.classList.add('hidden');
                        } else {
                            pointSection.style.display = 'none';
                            pointInfo.classList.remove('hidden');
                        }
                    } else {
                        memberPoints = 0;
                        pointsAmount.innerText = '0';
                        nameInput.value = '';
                        pointSection.style.display = 'none';
                        pointInfo.classList.add('hidden');
                    }
                })
                .catch(err => console.log('Error:', err));
        }
    }
    //Fungsi ini mengirimkan permintaan ke server untuk memeriksa apakah nomor telepon terdaftar sebagai member. Jika terdaftar:

    usePointsCheckbox.addEventListener('change', function () {
        if (this.checked && memberPoints > 0) {
            diskonPoin = Math.min(totalHarga, memberPoints);
            diskonPoinInput.value = diskonPoin;

            diskonPoinCell.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(diskonPoin);
            totalPriceCell.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga - diskonPoin);
            paymentInput.value = new Intl.NumberFormat('id-ID').format(totalHarga - diskonPoin);
        } else {
            diskonPoin = 0;
            diskonPoinInput.value = 0;

            diskonPoinCell.innerText = 'Rp 0';
            totalPriceCell.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga);
            paymentInput.value = new Intl.NumberFormat('id-ID').format(totalHarga);
        }
    });
    //buat ngecek poinnya di centang apa ngga

    paymentInput.addEventListener('input', function () {
        let value = this.value.replace(/\D/g, ''); //biar ga ada huruf selain nomer
        this.value = new Intl.NumberFormat('id-ID').format(value); //biar pake mata uang indo

        let numeric = parseInt(value || 0);
        const minimum = totalHarga - diskonPoin;
        reminder.classList.toggle('hidden', numeric >= minimum);
    });
    //buat ngasih tau kalo total uang yg di bayar kurang

    form.addEventListener('submit', function (e) {
        let cleanValue = paymentInput.value.replace(/\./g, '').replace('Rp', '').trim();
        let numeric = parseInt(cleanValue || 0);
        const minimum = totalHarga - diskonPoin;

        if (numeric < minimum) {
            e.preventDefault();
            reminder.classList.remove('hidden');
            paymentInput.focus();
            return false;
        }

        paymentInput.value = cleanValue;
    });
</script>
@endsection