<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Orders\OrderProductCreateRequest;
use App\Http\Requests\Orders\OrderProductUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderPrice;
use App\Models\OrderProduct;

class OrderProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($orderId)
    {
        $order = Order::find($orderId);

        if (empty($order)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Заказ не найден."
                ],
            ])->setStatusCode(404);
        }

        return response()->json([
            'code'      => 200,
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ])->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderProductCreateRequest $request, $orderId)
    {
        $product = $request->input();

        $order = Order::find($orderId);

        if (empty($order)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Заказ не найден."
                ],
            ])->setStatusCode(404);
        }

        if ($order->products()->where('product_id', $product["product_id"])->first()) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Продукт уже присутствует в заказе."
                ],
            ])->setStatusCode(422);
        }

        OrderProduct::addProductToOrder($order, $product);
        OrderPrice::setPriceToOrder($order);

        return response()->json([
            'code'      => 201,
            'message'   => "Товары заказа №{$order->id} изменены. Продукт №{$product["product_id"]} добавлен к заказу.",
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ])->setStatusCode(201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderProductUpdateRequest $request
     * @param $orderId
     * @param $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderProductUpdateRequest $request, $orderId, $productId)
    {
        $quantity = $request->input('quantity');

        $order = Order::find($orderId);
        $product = $order->products()->find($productId);

        if (empty($order)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Заказ не найден."
                ],
            ])->setStatusCode(404);
        }

        if ($product == null) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Товар №{$productId} не найден в заказе."
                ],
            ])->setStatusCode(422);
        }

        OrderProduct::updateProductInOrder($order, $product->id, $quantity);
        OrderPrice::setPriceToOrder($order);

        return response()->json([
            'code'      => 201,
            'message'   => "Товары заказа №{$order->id} изменены. Товар №{$product->id} в заказе изменен.",
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ])->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $orderId
     * @param $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($orderId, $productId)
    {
        $order = Order::find($orderId);
        $product = $order->products()->find($productId);

        if (empty($order)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Заказ не найден."
                ],
            ])->setStatusCode(404);
        }

        if ($product == null) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Товар №{$productId} не найден в заказе."
                ],
            ])->setStatusCode(422);
        }

        OrderProduct::removeProductFromOrder($order, $productId);
        OrderPrice::setPriceToOrder($order);

        return response()->json([
            'code' => 201,
            'message' => "Товары заказа №{$order->id} изменены. Продукт №{$product["id"]} удален из заказа.",
            'order' => OrderResource::make($order),
            'products' => ProductResource::collection($order->products),
        ])->setStatusCode(201);
    }
}
