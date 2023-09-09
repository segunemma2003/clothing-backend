<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded =['id'];
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function products(){
        return $this->hasMany(ProductOrder::class, 'order_id');
    }


    public function shipping(){
        return $this->hasOne(ShippingOrder::class, 'order_id');
    }
}
