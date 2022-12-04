<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'theme', 
        'theme_color', 
        'home_view_mode', 
        'product_view_mode', 
        'rajaongkir_type', 
        'rajaongkir_apikey', 
        'rajaongkir_couriers', 
        'warehouse_id', 
        'warehouse_address', 
        'tripay_api_key', 
        'tripay_private_key', 
        'tripay_mode', 
        'tripay_merchant_code', 
        'telegram_bot_token', 
        'telegram_user_id', 
        'is_notifypro',
        'is_payment_gateway',
        'is_guest_checkout',
        'is_whatsapp_checkout',
        'notifypro_interval', 
        'notifypro_timeout', 
        'cod_list', 
        'is_cod_payment',
        'service_fee',
        'service_fee_label',
        'is_service_fee',
        'is_shipping_active'
    ];
    public $appends = [
        'is_shippable', 
        'can_shipping', 
        'is_tripay_ready',
        'is_telegram_ready',
        'courier_available', 
        'is_bank_ready',
        'is_demo_mode',
        'demo_phone',
        'is_mail_ready',
        'can_cod',
        'tripay_callback'
    ];

    protected $hidden = [
        'rajaongkir_apikey',
        'tripay_api_key',
        'tripay_private_key',
        'tripay_merchant_code',
        'telegram_bot_token',
        'telegram_user_id',
    ];

    public $casts = [
        'is_notifypro' => 'boolean',
        'is_payment_gateway' => 'boolean',
        'is_cod_payment' => 'boolean',
        'is_whatsapp_checkout' => 'boolean',
        'is_guest_checkout' => 'boolean',
        'is_service_fee' => 'boolean',
        'is_shipping_active' => 'boolean',
        'review_auto_approved' => 'boolean',
        'cod_list' => 'array',
        'warehouse_address' => 'object'
    ];


    public function getIsShippableAttribute()
    {
        return $this->rajaongkir_apikey && $this->rajaongkir_type ? true : false;
    }
    public function getCanCodAttribute()
    {
        return $this->cod_list ? true : false;
    }
    public function getCanShippingAttribute()
    {
        if($this->rajaongkir_apikey && $this->rajaongkir_type && $this->warehouse_address && $this->rajaongkir_couriers) {
            return true;
        }
        return false;
    }
    public function getIsTripayReadyAttribute()
    {
        return $this->tripay_api_key && $this->tripay_private_key && $this->tripay_merchant_code && $this->is_payment_gateway ? true : false;
    }
    public function getIsTelegramReadyAttribute()
    {
        return $this->telegram_bot_token && $this->telegram_user_id ? true : false;
    }
    public function getIsMailReadyAttribute()
    {
        $mail = config('mail.mailers.smtp');
        if($mail && $mail['username'] && $mail['host'] && $mail['port'] && $mail['password']) {
            return true;
        }
        return false;
    }
    public function getIsBankReadyAttribute()
    {
        $countBankAccount = Cache::rememberForever('bank_account_count', function() {
            return BankAccount::count();
        });
        return  $countBankAccount > 0 ? true : false;
    }
    public function getCourierAvailableAttribute()
    {
        return [
            'pro' => config('rajaongkir.courier_pro'),
            'basic' => config('rajaongkir.courier_basic'),
            'starter' => config('rajaongkir.courier_starter')
        ];
    }
    public function getIsDemoModeAttribute()
    {
        return config('demo.is_demo');
    }
    public function getDemoPhoneAttribute()
    {
        return config('demo.phone');
    }

    public function getTripayCallbackAttribute()
    {
        return route('tripay.callback');
    }

}
