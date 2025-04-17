<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function checkMemberHistory(Request $request)
    {
        $member = Member::where('no_phone', $request->no_phone)->first();
        //buat ngecek lewat nomor telfon si member kalo g ada riwayat maka bakal di kirim response ke json jadi false 
        if (!$member) {
            return response()->json(['exists' => false]);
        }
        //kalo ada maka akan mengecek si member punya history beli ga, trs kalo ada maka hasPurchases true
        $hasPurchases = $member->purchases()->exists();

        return response()->json([
            'exists' => true,
            'name' => $member->name,
            'points' => $member->poin,
            'hasPurchases' => $hasPurchases
        //Jika member ditemukan, fungsi ini mengembalikan data member dalam format JSON, termasuk nama, jumlah poin, dan status apakah member memiliki pembelian sebelumnya ('hasPurchases').
        ]);
    }

    //buat nyari member berdasarkan no telp, itu tuh mem baru atau bukan
    public function getOrCreate(Request $request)
    {
        $request->validate([
            'no_phone' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);

        //buat nyari member pake no telp pernah ada atau baru buat, kalo baru buat maka poinnya akan diatur 0
        $member = Member::firstOrCreate(
            ['no_phone' => $request->no_phone],
            ['name' => $request->name, 'poin' => 0]
        );

        //dikembalikan sebagai respons
        return $member;
    }

    //buat ngitung poin
        public function applyPoints(Member $member, int $total_price)
    {
        $diskon_poin = 0; //semuanya nol dulu

        if ($member->poin > 0 && $member->purchases()->exists()) { //buat ngecek member punya riwayat pembelian ga kalo iya maka poin bisa di gunakan
            $max_potongan = $member->poin; //Menetapkan jumlah maksimal diskon berdasarkan poin yang dimiliki oleh member.

            if ($max_potongan >= $total_price) {
                $diskon_poin = $total_price;
                $total_price = 0;
            } else {
                $diskon_poin = $max_potongan;
                $total_price -= $diskon_poin;
            }

            $member->decrement('poin', $diskon_poin); //Mengurangi poin member berdasarkan jumlah diskon yang digunakan.

        }
        return [
            'total_price' => $total_price,
            'diskon_poin' => $diskon_poin
        ];
    }

    public function addPoints(Member $member, int $total_price) //buat nambah poin sesuai sama harga yg di bayar sama si member
    {
        $earned_points = floor($total_price * 0.01); //di ambil 1% dari harga, jadi harga total di kali 0.01 terus hasilnya di buletin ke bawah pake floor
        $member->increment('poin', $earned_points); //Menambahkan poin ke akun member berdasarkan jumlah poin yang dihitung.
    }
}
