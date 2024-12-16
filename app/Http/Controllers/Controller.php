<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

abstract class Controller
{
    public function saveImage($image, $path='public'){
        if(!$image){
            return null;
        }
        try {
            // Buat filename berdasarkan waktu saat ini
            $filename = time() . '.png';

            // Decode base64 string menjadi file
            $decodedImage = base64_decode($image);

            if ($decodedImage === false) {
                return response()->json(['error' => 'Invalid base64 image data'], 400);
            }

            // Simpan gambar ke storage disk
            Storage::disk($path)->put($filename, $decodedImage);

            // Mengembalikan URL file yang disimpan
            return URL::to('/') . '/storage/' . $path . '/' . $filename;

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
