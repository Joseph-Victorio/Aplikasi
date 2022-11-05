<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Config;
use App\Models\MailConfig;
use Illuminate\Http\Request;
use App\Mail\MailNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TelegramNotification;

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

        if(config('app.env') != 'production') {
            return response()->json([
                'success' => false,
                'message' => 'Development Mode',
                'env' => config('app.env')
            ]);
        };

        $order = Order::with('transaction')->find($request->order_id);

        if(!$order) {
            return response([
                'success' => false,
                'message' => 'No order found.'
            ], 200);
        }

        $result = [
            'success' => true,
            'admin_tele' => 'Not Define',
            'admin_mail' => 'Not Define',
            'user_mail' => 'Not Define',
        ];

        $customerPayload = generateUserEmailOrderCreated($order);
        $adminPayload = generateAdminEmailOrderCreated($order);

        $mailConfig = MailConfig::first();

        if($this->config->is_telegram_ready) {
            
            try {
                Notification::route('telegram', config('telegram.user_id'))
                ->notify(new TelegramNotification($adminPayload));

                $result['admin_tele'] = 'OK';

            } catch (\Throwable $th) {
                
                $result['admin_tele'] = $th->getMessage();
            }
        }

        if($this->config->is_mail_ready) {

            try {
                Mail::to($mailConfig->mail_admin)
                ->later(now()->addSeconds(10), new MailNotification($adminPayload));

                $result['admin_mail'] = 'OK';

            } catch (\Throwable $th) {
               $result['admin_mail'] = $th->getMessage();
            }

            try {

                Mail::to($order->customer_email)
                    ->later(now()->addSeconds(10), new MailNotification($customerPayload));

                $result['user_mail'] = 'OK';

            } catch (\Throwable $th) {
               $result['user_mail'] = $th->getMessage();
            } 

        }

        return response($result, 200);
        
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
