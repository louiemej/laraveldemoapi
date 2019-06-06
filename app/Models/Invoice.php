<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use App\Models\Payment;
// use App\Models\Item;

class Invoice extends Model
{
    public function payment() {
        return $this->hasOne('App\Models\Payment', 'invoice_id', 'id');
    }

    public function items() {
        return $this->hasMany('App\Models\Item', 'invoice_id', 'id');
    }
}
