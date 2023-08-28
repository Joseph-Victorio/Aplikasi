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
        'is_notifypro',
        'notifypro_interval',
        'notifypro_timeout',
        'cod_list',
        'is_cod_payment',
        'is_shipping_active',
        'review_auto_approved',
        'catalog_product_limit',
        'catalog_product_sort',
        'home_product_limit',
        'home_product_sort',
        'accent_color',
        'secondary_color',
        'primary_color',
    ];
    public $appends = [
        'is_shippable',
        'can_shipping',
        'courier_available',
        'is_mail_ready',
        'can_cod',
    ];

    protected $hidden = [
        'rajaongkir_apikey',
        'telegram_bot_token',
        'telegram_user_id',
    ];

    public $casts = [
        'is_notifypro' => 'boolean',
        'is_cod_payment' => 'boolean',
        'is_shipping_active' => 'boolean',
        'review_auto_approved' => 'boolean',
        'cod_list' => 'array',
        'warehouse_address' => 'object',
        'rajaongkir_couriers' => 'array'
    ];


    public function getIsShippableAttribute()
    {
        return $this->rajaongkir_apikey  && $this->rajaongkir_type ? true : false;
    }
    public function getCanShippingAttribute()
    {
        if (
            $this->rajaongkir_apikey
            && $this->rajaongkir_type
            && $this->warehouse_address
            && $this->rajaongkir_couriers
            && $this->is_shipping_active
        ) {
            return true;
        }
        return false;
    }
    public function getCanCodAttribute()
    {
        return $this->cod_list ? true : false;
    }
    public function getIsMailReadyAttribute()
    {
        $mail = config('mail.mailers.smtp');
        if ($mail && $mail['username'] && $mail['host'] && $mail['port'] && $mail['password']) {
            return true;
        }
        return false;
    }
    public function getCourierAvailableAttribute()
    {
        return [
            'pro' => config('rajaongkir.courier_pro'),
            'basic' => config('rajaongkir.courier_basic'),
            'starter' => config('rajaongkir.courier_starter')
        ];
    }
}
