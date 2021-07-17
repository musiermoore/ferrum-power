<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    public static function addProductToOrder($order, $products)
    {
        foreach ($products as $product) {
            $productPrice = Product::getProductPrice($product["product_id"]);

            $order->products()->attach(array($product["product_id"] => [
                'quantity'   => $product["quantity"],
                'full_price' => $product["quantity"] * $productPrice,
            ]));
        }
    }

    public static function removeProductFromOrder($order, $product)
    {
        $order->products()->wherePivot('product_id', $product["product_id"])->detach();
    }
}
