<?php

namespace App\Http\Controllers;

use App\Models\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MailConfigController extends Controller
{
    public function show()
    {
        return response()->json([
            'success' => true,
            'results' => MailConfig::first(),
            'mail' => config('mail')
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'smtp_host' => 'required',
            'smtp_port' => 'required|numeric',
            'smtp_username' => 'required',
            'smtp_password' => 'required',
            'smtp_encryption' => 'required',
            'from_name' => 'required',
            'from_address' => 'required',
            'mail_admin' => 'required',
            'is_active' => 'required',
        ]);
        
        $mailConfig = MailConfig::first();

        $mailConfig->update($data);

        Artisan::call('optimize:clear');

        return response()->json([
            'success' => true,
            'results' => $mailConfig->fresh(),
            'mail' => config('mail')
        ]);
    }

}
