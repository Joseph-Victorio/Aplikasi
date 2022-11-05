<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

if(!function_exists('getSessionUser')) {

  function getSessionUser() {
    
    $sessionUser = request()->server('HTTP_SESSION_USER');

    return $sessionUser;

  }

}

function setCurrentMailConfig($mailConfig) {

  if($mailConfig->is_ready) {

    $cfg = [
        'transport' => 'smtp',
        'host' => $mailConfig->smtp_host,
        'port' => $mailConfig->smtp_port,
        'username' => $mailConfig->smtp_username,
        'password' => $mailConfig->smtp_password,
        'encryption' => Str::lower($mailConfig->smtp_encryption),
        'timeout' => null,
        'auth_mode' => null,
    ];

    Config::set('mail.mailers.smtp', $cfg);

    $form  = [
        'address' => $mailConfig->from_address,
        'name' => $mailConfig->from_name,
    ];

    Config::set('mail.from', $form);
  }
}

function generateUserEmailOrderCreated($order)
{
  $appName = config('app.name');

  $courier = $order->shipping_courier_name;
  $total = 'Rp. ' . number_format($order->order_total,0,",",".");
  $tgl = Carbon::parse($order->created_at)->format('d/m/Y  H:i');
  $expiredDate = Carbon::createFromTimestamp($order->transaction->expired_time)->format('d/m/Y H:i');

  $body = "Halo, Yth $order->customer_name \nTerima kasih sudah berbelanja di $appName\nBerikut detil pesanan anda:\nNama : $order->customer_name \nInvoice : $order->order_ref\nDibuat : $tgl\nTotal :  $total\nKurir : $courier\n\nSegera lakukan pembayaran sebelum $expiredDate agar pesanan anda dapat diproses, Terimakasih.";

    return [
      'customer_email' => $order->customer_email,
      'subject' => 'Notifikasi pesanan baru',
      'body' => $body,
      'action' => [
        'action_url' => route('invoice', $order->order_ref),
        'action_label' => 'View Order'
      ]
    ];
  }
function generateAdminEmailOrderCreated($order)
{
  $methodArr = explode('_', $order->transaction->payment_method);
  $paymentMethod = join(' ', $methodArr);
  $appName = config('app.name');
  $invoiceId = $order->order_ref;
  $name = $order->customer_name;
  $email = $order->customer_email;
  $phone = $order->customer_whatsapp;
  $courier = $order->shipping_courier_name;
  $total = 'Rp. ' . number_format($order->order_total,0,",",".");

  $body = "Halo Admin $appName,\nSelamat ada pesanan baru!\nDetil Pesanan:\nInvoice : $invoiceId\nNama   : $name\nEmail: $email\nPhone: $phone\nOrder Total : $total\nKurir : $courier\nMetode Pembayaran : $paymentMethod";

    return [
      'customer_email' => $order->customer_email,
      'admin_email' => $order->customer_email,
      'subject' => 'Notifikasi pesanan baru',
      'body' => $body,
      'action' => [
        'action_url' => route('invoice', $order->order_ref),
        'action_label' => 'View Order'
      ]
    ];
  }