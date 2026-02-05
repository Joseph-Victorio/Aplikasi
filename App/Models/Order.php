<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
   use HasFactory;

   protected $guarded = [];
   public $appends = ['status_label', 'customer_status_label', 'created', 'grand_total', 'bukti_tf'];

   public function getGrandTotalAttribute()
   {
      return $this->order_total + $this->payment_fee;
   }

   public function getBuktiTfAttribute()
   {
      return $this->attributes['bukti_tf']
         ? asset($this->attributes['bukti_tf'])
         : null;
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
      return $this->created_at->format('d/m/Y ~ H:i');
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
         case 'AWAITING_PICKUP':
            return 'Menunggu Diambil';
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
         case 'AWAITING_PICKUP':
            return 'Menunggu Diambil';
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
   protected static function boot()
   {
      parent::boot();

      static::creating(function ($model) {
         $nextId = (static::latest('id')->value('id') ?? 0) + 1;

         $model->order_ref = 'INV'
            . Carbon::now()->format('ym')
            . str_pad($nextId, 5, '0', STR_PAD_LEFT)
            . Str::upper(Str::random(2));
      });
   }
}
