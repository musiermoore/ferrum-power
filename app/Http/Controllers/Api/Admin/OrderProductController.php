<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Orders\OrderProductCreateRequest;
use App\Http\Requests\Orders\OrderProductUpdateRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderPrice;
use App\Models\OrderProduct;

class OrderProductController extends AdminBaseController
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
            return $this->errorResponse(404, "Заказ не найден.");
        }

        $data = [
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ];

        return $this->successResponse(200, null, $data);
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
            return $this->errorResponse(404, "Заказ не найден.");
        }

        if ($order->products()->where('product_id', $product["product_id"])->first()) {
            return $this->errorResponse(400, "Продукт уже присутствует в заказе.");
        }

        OrderProduct::addProductToOrder($order, $product);
        OrderPrice::setPriceToOrder($order);

        $data = [
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ];

        $message = "Товары заказа №{$order->id} изменены. Продукт №{$product["product_id"]} добавлен к заказу.";
        return $this->successResponse(201, $message, $data);
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
            return $this->errorResponse(404, "Заказ не найден.");
        }

        if (empty($product)) {
            return $this->errorResponse(400, "Товар №{$productId} не найден в заказе.");
        }

        OrderProduct::updateProductInOrder($order, $product->id, $quantity);
        OrderPrice::setPriceToOrder($order);

        $data = [
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ];

        $message = "Товары заказа №{$order->id} изменены. Товар №{$product->id} в заказе изменен.";
        return $this->successResponse(201, $message, $data);
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
            return $this->errorResponse(404, "Заказ не найден.");
        }

        if (empty($product)) {
            return $this->errorResponse(400, "Товар №{$productId} не найден в заказе.");
        }

        OrderProduct::removeProductFromOrder($order, $productId);
        OrderPrice::setPriceToOrder($order);

        $data = [
            'order' => OrderResource::make($order),
            'products' => ProductResource::collection($order->products),
        ];

        $message = "Товары заказа №{$order->id} изменены. Продукт №{$product["id"]} удален из заказа.";
        return $this->successResponse(200, $message, $data);
    }
}
