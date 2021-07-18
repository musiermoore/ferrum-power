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

    public static function removeProductFromOrder($order, $productId)
    {
        $order->products()->wherePivot('product_id', $productId)->detach();
    }

    public static function updateProductInOrder($order, $productId, $quantity)
    {
        $productPrice = Product::getProductPrice($productId);
        $order->products()
            ->where('product_id', $productId)
            ->update(['quantity' => $quantity, 'full_price' => $productPrice * $quantity]);
    }
}
