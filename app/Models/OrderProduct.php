<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'order_id',
        'full_price',
        'quantity'
    ];

    public static function addProductToOrder($order, $product)
    {
        $productPrice = Product::getProductPrice($product["product_id"]);

        $order->products()->attach(array($product["product_id"] => [
            'quantity'   => $product["quantity"],
            'full_price' => $product["quantity"] * $productPrice,
        ]));
    }

    public static function addProductsToOrder($order, $products)
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

    public static function updateProductInOrder($order, $product)
    {
        $productPrice = Product::getProductPrice($product["product_id"]);
        $order->products()
            ->where('product_id', $product["product_id"])
            ->update(['quantity' => $product["quantity"], 'full_price' => $productPrice * $product["quantity"]]);
    }
}
