<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Helper\HelperResponse;
use InvalidArgumentException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use HelperResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->errors());
        }
        $emaildipakai = User::where('email', $request->input('email'))->first();

        if ($emaildipakai) {
            return response()->json([
                'error' => 'The email address has already been taken.'
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ], 'User created successfully', 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Email atau password salah'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
        $user = Auth::user();

        return $this->successResponse(
            [   'id' => $user->id,
                'nama' => $user->name,
                'token' => $token,
            ],
            'berhasil login',
            200
        );
    }

    public function deleteUser($id)
    {
        try {
            
            DB::beginTransaction();
            $user = User::find($id);
            $user->posts()->delete();
            $user->delete();
            DB::commit();

            return response()->json([
                'message' => 'user dan post anda berhasil dihapus'
            ], 200);

        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollBack();

            // Kembalikan response error
            return response()->json([
                'error' => 'gagal hapus user',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function getUser()
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

}
