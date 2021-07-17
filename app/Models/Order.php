<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'description',
        'address',
        'order_status_id',
        'operator_id',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products');
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function orderStatus()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function orderPrice()
    {
        return $this->hasOne(OrderPrice::class);
    }

    /**
     * @param $query
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public static function searchOrdersByQuery($query)
    {
        $name = $query["name"];
        $phone = $query["phone"];

        $orders = Order::query()
            ->when($name, function ($query) use ($name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->when($phone, function ($query) use ($phone) {
                return $query->where('phone', 'like', '%' . $phone . '%');
            })
            ->get();

        return $orders;
    }

    public function setDefaultOrderStatus()
    {
        $this->order_status_id = OrderStatus::DEFAULT_ORDER_STATUS_ID;
    }
}
