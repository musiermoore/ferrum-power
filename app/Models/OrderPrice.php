<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPrice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'order_price'
    ];

    public static function setPriceToOrder($order)
    {
        $orderPrice = $order->products()->sum('full_price');
        $order->orderPrice()->create(['order_price' => $orderPrice]);
    }
}
