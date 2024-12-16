<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{

    //get all
    public function users() {
        $users = User::all();
        return response()->json(['users' => $users]);
    }
    public function register(Request $request)
    {
        //validasi
        $user = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'notelp' => 'required|string|digits:12',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|string'
        ]);

        //set default role mhs
        $role = $request->input('role', 'mahasiswa');
        //create user
        $user = User::create([
            'name' => $user['name'],
            'email' => $user['email'],
            'notelp' => $user['notelp'],
            'password' => bcrypt($user['password']),
            'role' => $role
        ]);

        //return user & token in response
        return response([
            'user' => $user,
            // 'token'=>$request->createToken($request->token_name), //give a secret to sanctum to verify tokens with

            'token' => $user->createToken('secret')->plainTextToken,
        ], 200);
    }
    public function login(Request $request)
    {
        //validasi
        $user = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        //cek
        if (!Auth::attempt($user)) {
            return response(
                [
                    'message' => 'invalid credentials'
                ],
                403
            );
        }
        $user = Auth::user();
        $token = $user->createToken('secret')->plainTextToken;

        //return user & token in response
        return response([
            'user' => $user,
            // 'token'=>$user->createToken('auth_token')->plainTextToken,
            'token' => $token,
        ], 200);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response([
            'message' => 'Logout success'
        ], 200);
    }

    //get user details
    public function user()
    {
        $user = Auth::user();
        return response([
            'user' => $user
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'notelp' => 'nullable|string',
            'role'=>'nullable|string'
        ]);

        $image = $this->saveImage($request->image, 'profil');

        $user = User::find($id);

        if(!$user){
            return response([
                'message'=>'User tidak ditemukan.'
            ], 404);
        }

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }
        if (isset($data['notelp'])) {
            $user->notelp = $data['notelp'];
        }
        if (isset($data['role'])) {
            $user->role = $data['role'];
        }
    
        $user->save();
    
        

        return response([
            'status' => 'success',
            'message' => 'User berhasil diubah.'
        ], 200);
    }


    public function destroy($id){
        $user=User::find($id);

        if(!$user){
            return response([
                'message'=>'User tidak ditemukan.'
            ], 404);
        }
        $user->delete();
        return response([
            'message'=>'User berhasil dihapus.'
        ],200);
    }

    public function getTotalUser(){
        $totalUser=User::count();
        return response()->json(['total_user'=>$totalUser], 200);
    }
}
