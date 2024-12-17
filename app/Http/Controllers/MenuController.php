<?php

namespace App\Http\Controllers;

use App\Models\Kantin;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;  // Menambahkan import Storage

class MenuController extends Controller
{
    public function index($id){
        $kantin = Kantin::find($id);

        if (!$kantin) {
            return response([
                'status' => 'error',
                'message' => 'Kantin tidak ditemukan'
            ], 404);
        }

        return response([
            'kantin' => $kantin->nama_kantin,
            'menu' => $kantin->menu()
                ->select('id', 'nama_menu', 'harga', 'deskripsi', 'stok', 'image', 'id_kantin')
                ->get()
        ], 200);
    }

    public function show($id_kantin, $id){
        $kantin = Kantin::find($id_kantin);

        if (!$kantin) {
            return response([
                'status' => 'error',
                'message' => 'Kantin tidak ditemukan'
            ], 404);
        }

        $menu = Menu::where('id_kantin', $id_kantin)->where('id', $id)->first();

        if (!$menu) {
            return response([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        return response([
            'kantin' => $kantin->nama_kantin,
            'menu' => $menu
        ], 200);
    }

    public function store(Request $request, $id){
        $kantin = Kantin::find($id);

        if (!$kantin) {
            return response([
                'status' => 'error',
                'message' => 'Kantin tidak ditemukan.'
            ], 404);
        }

        // cek user yg login punya kantin ga (id user ada di kantin ga)
        if ($kantin->id_karyawan != Auth::id()) {
            return response([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menambah menu di kantin ini.'
            ], 403);
        }

        $data = $request->validate([
            'nama_menu' => 'required|string|unique:menu,nama_menu',
            'deskripsi' => 'required|string',
            'harga' => 'required|string',
            'image' => 'nullable|string',
            'stok' => 'required|integer',
        ]);

        try {
            // Mengunggah gambar jika ada
            $image = null;
            if ($request->hasFile('image')) {
                $image = $this->saveImage($request->image, 'kantin');
            }

            Menu::create([
                'nama_menu' => $data['nama_menu'],
                'harga' => $data['harga'],
                'deskripsi' => $data['deskripsi'],
                'image' => $image,
                'stok' => $data['stok'],
                'id_kantin' => $kantin['id'],
            ]);

            return response([
                'status' => 'success',
                'message' => 'Menu berhasil ditambahkan.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id){
        $kantin = Kantin::with('menu')->find($id);
        $menu = Menu::with('kantin')->find($id);
        $user = Auth::user();

        // cek user punya kantin ga
        if ($kantin->id_karyawan != $user->id) {
            return response([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk mengubah menu di kantin ini.'
            ], 403);
        }

        if (!$menu) {
            return response([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }

        $data = $request->validate([
            'nama_menu' => 'string|unique:menu,nama_menu',
            'deskripsi' => 'string',
            'harga' => 'string',
            'image' => 'string',
            'stok' => 'integer',
        ]);

        $image = $menu->image;
        if ($request->hasFile('image')) {
            if ($image) {
                Storage::delete('public/' . $image); // Hapus gambar lama
            }
            $image = $request->file('image')->store('menu_images', 'public');
        }

        $menu->update(array_merge($data, ['image' => $image]));

        return response([
            'status' => 'success',
            'message' => 'Menu berhasil diubah.'
        ], 200);
    }

    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response([
                'status' => 'error',
                'message' => 'Menu tidak ditemukan.'
            ], 404);
        }

        $kantin = $menu->kantin;
        if ($kantin->id_karyawan != Auth::id()) {
            return response([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses untuk menghapus menu di kantin ini.'
            ], 403);
        }

        if ($menu->image) {
            Storage::delete('public/' . $menu->image);
        }

        $menu->delete();

        return response([
            'status' => 'success',
            'message' => 'Menu berhasil dihapus.'
        ], 200);
    }

    public function getTotalMenu($id_kantin)
    {
        $totalMenu = Menu::where('id_kantin', $id_kantin)->count();
        return response()->json(['total_menu' => $totalMenu], 200);
    }
}
