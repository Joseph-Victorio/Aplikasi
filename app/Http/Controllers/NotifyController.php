<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Order;
use App\Models\Config;
use App\Models\MailConfig;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Mail\MailNotification;
use Illuminate\Support\Facades\Mail;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;

class NotifyController extends Controller
{
    protected $config;
    public function __construct()
    {
        $this->config =  Config::first();
    }
    public function sendOrderNotify(Request $request)
    {

        $request->validate([
            'order_id' => 'required'
        ]);

        $result = [
            'telegram_admin' => 'Not Defined',
            'mailed_admin' => 'Not Defined',
            'mailed_user' => 'Not Defined',
        ];

        if(config('app.env') != 'production') {
            return response()->json(['success' => false, 'message' => 'Development mode']);
        };

        try {

            $order = Order::with('transaction')->find($request->order_id);
            
            if(!$order) {
                throw new Exception("Order not found");
            }

            $customerPayload = generateUserEmailOrderCreated($order);
            $adminPayload = generateAdminEmailOrderCreated($order);

            $mailConfig = MailConfig::first();

            if($this->config->is_telegram_ready) {
                
                try {
                    Notification::route('telegram', config('telegram.user_id'))
                    ->notify(new TelegramNotification($adminPayload));

                    $result['telegram_admin'] = 'Success';

                } catch (\Throwable $th) {
                    
                    $result['telegram_admin'] = $th->getMessage();
                }
            }

            if($this->config->is_mail_ready) {

                try {
                    Mail::to($mailConfig->mail_admin)
                    ->later(now()->addSeconds(10), new MailNotification($adminPayload));

                    $result['mailed_admin'] = 'Success';

                } catch (\Throwable $th) {
                $result['mailed_admin'] = $th->getMessage();
                }

                try {

                    Mail::to($order->customer_email)
                        ->later(now()->addSeconds(10), new MailNotification($customerPayload));

                    $result['mailed_user'] = 'Success';

                } catch (\Throwable $th) {
                $result['mailed_user'] = $th->getMessage();
                } 

            }

            return ApiResponse::success($result);
        
        } catch (\Throwable $th) {
            return ApiResponse::failed($th);
        }
        
    }

    public function testingTelegram()
    {
        $res = [
            'success' => true,
            'results' => [
                'type' => 'positive',
                'message' => 'Berhasil mengirim telegram'
            ]
        ];

        if($this->config->is_telegram_ready) {

            try {

                $message= "Halo admin!\nTesting notifikasi telegram berhasil";

                Notification::route('telegram', config('telegram.user_id'))
                    ->notify(new TelegramNotification($message));
    
            } catch (\Throwable $th) {
    
                $res['success']= false;
                $res['results']['type'] = 'negative';
                $res['results']['message'] = $th->getMessage();
            }
            
        }else {
            $res['success']= false;
                $res['results']['type'] = 'negative';
                $res['results']['message'] = 'Pengaturan telegram belum sesuai';
        }

        return response()->json($res);
        
    }
    public function testingEmail()
    {
        $res = [
            'success' => true,
            'results' => [
                'type' => 'positive',
                'message' => 'Berhasil mengirim email'
            ]
        ];

        $mailConfig = MailConfig::first();
        
        if($mailConfig->is_ready) {
            
            try {
                
                $payload = [
                    'subject' => "Testing email notifikasi",
                    'body' => "Halo admin!\nTesting notifikasi smtp berhasil",
                ];

                Mail::to($mailConfig->mail_admin)->send(new MailNotification($payload));
    
            } catch (\Throwable $th) {
    
                $res['success']= false;
                $res['results']['type'] = 'negative';
                $res['results']['message'] = $th->getMessage();
            }
            
        }else {
            $res['success']= false;
            $res['results']['type'] = 'negative';
            $res['results']['message'] = 'Pengaturan email belum sesuai';
        }

        return response()->json($res);
        
    }
}
