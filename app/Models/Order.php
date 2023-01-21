<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $appends = ['status_label', 'customer_status_label', 'created', 'grand_total'];

    public function getGrandTotalAttribute()
    {
        return $this->order_total+$this->payment_fee;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function getCreatedReadableAttribute()
    {
        return $this->created_at->diffForHumans();
    }
    public function getCreatedAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
        
    }
    public function getStatusLabelAttribute()
    {
        switch ($this->order_status) {
            case 'CANCELED':
                return 'Batal';
                break;
            case 'TOSHIP':
                return 'Perlu Dikirim';
                break;
            case 'SHIPPING':
                return 'Dikirim';
                break;
            case 'COMPLETE':
                return 'Selesai';
                break;
            
            default:
            return 'Pending';
                break;
        }

    }
    public function getCustomerStatusLabelAttribute()
    {
        switch ($this->order_status) {
            case 'CANCELED':
                return 'Batal';
                break;
            case 'TOSHIP':
                return 'Diproses';
                break;
            case 'SHIPPING':
                return 'Dikirim';
                break;
            case 'COMPLETE':
                return 'Selesai';
                break;
            
            default:
            return 'Pending';
                break;
        }

    }
}
