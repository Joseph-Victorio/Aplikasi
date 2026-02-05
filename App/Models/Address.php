<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public $appends = ['label', 'value'];

    public function getValueAttribute()
    {
        return $this->id;
    }

    public function getLabelAttribute()
    {
        return $this->subdistrict_name . ', ' .  $this->type . ' ' . $this->city . ', ' . $this->province;
    }
}
