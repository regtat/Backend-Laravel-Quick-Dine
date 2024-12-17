<?php

namespace App\Http\Controllers;

use App\Models\Kantin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KantinController extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();
    
        try {
            if ($user->role == 'Mahasiswa' || $user->role == 'Admin') {
                $kantin = Kantin::all();
            } else {
                $kantin = Kantin::with('user:id,name')->where('id_karyawan', $user->id)->get();
            }
    
            return response([
                'kantin' => $kantin
            ], 200);
            
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    

    //index comment id
    public function show($id){
        $kantin = Kantin::find($id);

    if (!$kantin) {
        return response([
            'status' => 'error',
            'message' => 'Kantin tidak ditemukan.'
        ], 404);
    }

    return response([
        'kantin' => $kantin
    ], 200);
    }

    public function getUsers(){
        $user=User::select('id','name','email','role')->get();
        return response([
            'users'=>$user
        ],200);
    }
    public function store(Request $request){
        $kantin=$request->validate([
            'nama_kantin'=>'required|string|unique:kantin,nama_kantin', //biar gada duplikasi
            'id_karyawan' => 'required|exists:users,id', //validasi bahwa id_karyawan harus ada di tabel users
            'metode_pembayaran'=>'required|string',
            'no_telp'=>'required|string'
        ]);

        $kantin=Kantin::create([
            'nama_kantin'=>$kantin['nama_kantin'],
            'id_karyawan'=>$kantin['id_karyawan'],
            'metode_pembayaran'=>$kantin['metode_pembayaran'],
            'no_telp'=>$kantin['no_telp']
        ]);

        return response([
            'message'=>'Kantin berhasil ditambahkan.',
            'kantin'=>$kantin
        ], 200);
    }

    public function update(Request $request, $id){
        $kantin=Kantin::find($id);
        // $user=Auth::user();

        if(!$kantin){
            return response([
                'status'=>'error',
                'message'=>'Kantin tidak ditemukan.'
            ], 404);
        }
        $user=Auth::user();
        if($user->role!='admin' && $kantin->id_karyawan!= $user){
            return response([
                        'status'=>'error',
                        'message'=>'Anda tidak memiliki akses untuk mengedit kantin ini.'
                    ], 404);
        }
        // if($kantin->id_karyawan != $user->id){
        //     return response([
        //         'status'=>'error',
        //         'message'=>'Anda tidak memiliki akses untuk mengedit kantin ini.'
        //     ], 404);
        // }

        $data=$request->validate([
            'nama_kantin'=>'string',
            'id_karyawan'=>'integer',
            'metode_pembayaran'=>'string',
            'no_telp'=>'string'
        ]);

        $kantin->update($data);

        return response([
            'message'=>'Kantin berhasil diubah.'
        ], 200);
    }

    public function destroy($id){
        $kantin=Kantin::find($id);

        if(!$kantin){
            return response([
                'message'=>'Kantin tidak ditemukan.'
            ], 404);
        }
        $kantin->delete();
        return response([
            'message'=>'Kantin berhasil dihapus.'
        ],200);
    }

    public function getTotalKantin(){
        $totalKantin=Kantin::count();
        return response()->json(['total_kantin'=>$totalKantin], 200);
    }
}
