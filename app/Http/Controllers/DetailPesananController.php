<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DetailPesananController extends Controller
{
    public function index($id_pesanan)
    {
        $pesanan=Pesanan::findOrFail($id_pesanan);
        $detailPesanan = DetailPesanan::where('id_pesanan', $id_pesanan)->with('menu')->get();

        return response([
            'pesanan'=>$pesanan,
            'detail_pesanan' => $detailPesanan
        ], 200);
    }

    public function show($id_pesanan, $id)
    {
        $detail = DetailPesanan::where('id_pesanan', $id_pesanan)->where('id',$id)
        ->first();

        if (!$detail) {
            return response([
                'status' => 'error',
                'message' => 'Detail pesanan tidak ditemukan.'
            ], 404);
        }

        return response([
            'detail' => $detail
        ], 200);
    }

    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'id_menu' => 'required|exists:menu,id',
    //         'jumlah' => 'required|integer',
    //         'id_pesanan'=>'required|exists:pesanan,id'
    //     ]);

    //     //ambil info menu dan pesanan dari db
    //     $menu = Menu::findOrFail($data['id_menu']);

    //     $subtotal = $menu->harga * $data['jumlah'];

    //     //menambah detail pesanan
    //     // $detail = DetailPesanan::create([
    //     //     'id_pesanan' => $data['id_pesanan'],
    //     //     'id_menu' => $data['id_menu'],
    //     //     'jumlah' => $data['jumlah'],
    //     //     'subtotal' => $subtotal,
    //     // ]);

    //     return response([
    //         'message' => 'Detail pesanan berhasil ditambahkan.',
    //         'detail' => $detail
    //     ], 200);
    // }

    public function destroy($id)
    {
        $detail = DetailPesanan::find($id);

        if (!$detail) {
            return response([
                'message' => 'Detail pesanan tidak ditemukan.'
            ], 403);
        }
        $detail->delete();
        return response([
            'message' => 'Detail pesanan berhasil dihapus.'
        ], 200);
    }
}
