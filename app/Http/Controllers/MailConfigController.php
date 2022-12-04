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
