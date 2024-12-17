<?php

namespace App\Http\Controllers;

use App\Models\DetailPesanan;
use App\Models\Menu;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    public function index(Request $request, $id_kantin){
        //ambil user yg sedang login
        $user=$request->user();
        
        //jika user ingin melihat pesanan miliknya sendiri
    if ($user->role == 'mahasiswa') {
        $pesanan = Pesanan::where('id_user', $user->id)
                    ->with('user:id,name', 'kantin:id,nama_kantin')
                    ->get();

        return response([
            'pesanan' => $pesanan
        ], 200);
    }

    //jika karyawan ingin melihat pesanan kantin mereka
    if ($user->role == 'karyawan' && $id_kantin) {
        $pesanan = Pesanan::where('id_kantin', $id_kantin)
                    ->with('user:id,name', 'kantin:id,nama_kantin')
                    ->get();

        return response([
            'pesanan' => $pesanan
        ], 200);
    }
}

    public function show($id){
        $pesanan=Pesanan::with('detailPesanan', 'kantin')->find($id);

        if(!$pesanan){
            return response([
                'status'=>'error',
                'message'=>'Pesanan tidak ditemukan.'
            ], 404);
        }

        return response([
            'pesanan'=>$pesanan
        ], 200);
    }

    public function store(Request $request, $id_kantin){
        
        $data=$request->validate([
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran' => 'nullable|string',
            'diantar_diambil' => 'required|string',
            'lok_pengantaran' => 'nullable|string',
            'detail'            => 'required|array',
            'detail.*.id_menu'   => 'required|exists:menu,id',
            'detail.*.jumlah'    => 'required|integer|min:1'
        ]);
        
        $total = 0;
        $detailPesanan=[];
        foreach ($data['detail'] as $detail) {
            $menu = Menu::findOrFail($detail['id_menu']);
            if($menu->stok<$detail['jumlah']){
                return response([
                    'message'=> "Stok tidak mencukupi untuk menu {$menu->nama_menu}"
                ],400);
            }
            $subtotal=$menu->harga*$detail['jumlah'];
            $total += $subtotal;

            //simpan sementara
            $detailPesanan[]=[
                'id_menu' => $detail['id_menu'],
            'jumlah' => $detail['jumlah'],
            'subtotal' => $subtotal
            ];
        }


        $user=Auth::user();
        if (!$user) {
            return response(['message' => 'Unauthorized.'], 401);
        }
        $buktiPembayaran = isset($request->bukti_pembayaran) ? $request->bukti_pembayaran : null;
        $lokPengantaran = isset($request->lok_pengantaran) ? $request->lok_pengantaran : null;

        //membuat pesanan
        $pesanan=Pesanan::create([
            'id_user'=>$user->id,
            'id_kantin'=>$id_kantin,
            'metode_pembayaran'=>$data['metode_pembayaran'],
            'bukti_pembayaran'=>$buktiPembayaran,
            'diantar_diambil'=>$data['diantar_diambil'],
            'lok_pengantaran'=>$lokPengantaran,
            'total'=>$total,
            'status'=>'menunggu'
        ]);
        foreach($detailPesanan as $detail){
            DetailPesanan::create([
                'id_pesanan'=>$pesanan->id,
                'id_menu'=>$detail['id_menu'],
                'jumlah'=>$detail['jumlah'],
                'subtotal'=>$detail['subtotal']
                
            ]);

            //kurangi stok
            $menu = Menu::findOrFail($detail['id_menu']);
            $menu->stok -= $detail['jumlah'];
            $menu->save();
        }

        return response([
            'message'=>'Pesanan berhasil dibuat.',
            'pesanan'=>$pesanan
        ], 200);
    }

    public function updateStatus(Request $request, $id){
        $pesanan=Pesanan::findOrFail($id);
        $user=Auth::user();

        if($pesanan->id_kantin != $user->id_kantin){
            return response([
                'status'=>'error',
                'message'=>'Pesanan bukan milik kantin Anda.'
            ], 403);
        }
        $data=$request->validate([
            'status' => 'string'
        ]);

        $pesanan->update($data);

        return response([
            'message'=>'Status pesanan diperbarui.'
        ], 200);
    }

    public function destroy($id){
        $pesanan=Pesanan::find($id);

        if(!$pesanan){
            return response([
                'message'=>'Pesanan tidak ditemukan.'
            ], 403);
        }
        $pesanan->delete();
        return response([
            'message'=>'Pesanan berhasil dihapus.'
        ],200);
    }

    public function getTotalPesanan(){
        $totalPesanan=Menu::count();
        return response()->json(['total_pesanan'=>$totalPesanan], 200);
    }
}
