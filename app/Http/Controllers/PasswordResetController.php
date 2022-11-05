<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Notifications\ResetPasswordNotification;

class PasswordResetController extends Controller
{
    public function requestPasswordToken(Request $request)
    {

        $request->validate([
            'email' => ['required']
        ]);

        $user = User::where('email', $request->email)
            ->orWhere('phone', $request->email)
            ->first();

        if(!$user) {
            return response([
                'success' => false,
                'message' => 'Email atau nomor ponsel tidak terdaftar.'
            ], 200);
        }

        $isReady = PasswordReset::where('email', $user->email)->first();

        if($isReady) {

            if(Carbon::parse($isReady->created_at)->addHour() > now()) {
                return response([
                    'success' => false,
                    'message' => 'Anda baru saja membuat permintaan request password, Tunggu 60 menit untuk membuat permintaan kembali.'
                ], 200);
            }
            PasswordReset::where('email', $request->email)->delete();
        }

        DB::beginTransaction();
        
        try {
            $token = Str::upper(Str::random(4)) . rand(10,99) . Str::upper(Str::random(4));

            PasswordReset::create([
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]);

            $delay = now()->addSeconds(10);
            
            $user->notify((new ResetPasswordNotification($token))->delay($delay));

            DB::commit();

            $theEmail = $this->hideEmail($user->email);

            return response([
                'success' => true,
                'email' => $this->hideEmail($user->email),
                'message' => "Permintaan reset password berhasil, Silahkan buka email $theEmail"
            ], 200);

        } catch (\Throwable $th) {
            //throw $th;

            DB::rollBack();
            
            return response([
                'success' => false,
                'message' => $th->getMessage(),
            ], 200);
        }

        return response([
            'success' => true,
            'token' => $token,
            'email' => $request->email
        ], 200);
        
    }
    public function validateToken($token)
    {

        $tkn =  trim(htmlspecialchars(strip_tags($token)));

        if($tkn) {

            if( $data = PasswordReset::where('token', $tkn)->first()) {
                return response([
                    'success' => true,
                    'data' => $data
                ], 200);
            }
        }
       
        return response([
            'success' => false,
            'message' => 'Token salah, buka email anda dan masukkan token yang benar'
        ], 200);

    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
    ]);

        $tkn = filter_var($request->token, FILTER_SANITIZE_SPECIAL_CHARS);
        
        $data = PasswordReset::where('token', $tkn)->first();
        $user = User::where('email', $data->email)->first();

        if(!$data || !$user) {
            return response([
                'message' => 'Not Authenticated'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        PasswordReset::where('email', $data->email)->delete();

        return response([
            'success' => true,
            'message' => 'Silahkan Login dengan password baru anda.'
        ], 200);
    }

    protected function hideEmail($email) 
    {

        $em   = explode("@",$email);
        $name = implode('@', array_slice($em, 0, count($em)-1));
        $len = strlen($name)/2;

        return substr($name,0, floor($len)) . str_repeat('*', ceil($len)) . "@" . end($em);   

    }
}
