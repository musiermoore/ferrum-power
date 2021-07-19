<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\OrderCreateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderPrice;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  OrderCreateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderCreateRequest $request)
    {
        $data = $request->input();

        $checkProducts = array_key_exists("products", $data);

        if ($checkProducts) {
            $productId = Product::checkProductsAvailability($data["products"]);
            if (! empty($productId)) {
                return response()->json([
                    'error' => [
                        'code'      => 422,
                        'message'   => "Продукт №{$productId} не найден. Свяжитесь с оператором, чтобы решить проблему."
                    ],
                ])->setStatusCode(422);
            }
        }

        $order = Order::create($data);
        $order->setDefaultOrderStatus();

        if ($checkProducts) {
            OrderProduct::addProductsToOrder($order, $data["products"]);
            OrderPrice::setPriceToOrder($order);
        }

        return response()->json([
            'code'      => 201,
            'message'   => "Заказ отправлен. Номер заказа: №{$order->id}.",
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ])->setStatusCode(201);
    }
}
