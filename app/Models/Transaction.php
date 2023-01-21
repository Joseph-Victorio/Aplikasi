<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $appends = [
        'status_label'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function getStatusLabelAttribute()
    {
        if($this->status == 'PAID') {
            return 'Dibayar';
        }else {
            return 'Belum Bayar';
        }
    }
}
