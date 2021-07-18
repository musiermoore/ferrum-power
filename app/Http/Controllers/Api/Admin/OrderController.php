<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Orders\OrderCreateRequest;
use App\Http\Requests\Orders\OrderSearchRequest;
use App\Http\Requests\Orders\OrderUpdateRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\OrderPrice;
use App\Models\OrderProduct;
use App\Models\Product;

class OrderController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param OrderSearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(OrderSearchRequest $request)
    {
        $orders = Order::searchOrdersByQuery($request->query());

        return response()->json([
            'code'      => 200,
            'orders'    => OrderCollection::make($orders),
        ]);
    }

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

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $order = Order::find($id);

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
            'orders'    => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  OrderUpdateRequest  $request
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderUpdateRequest $request, $id)
    {
        $data = $request->input();

        $order = Order::find($id);

        if (empty($order)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Заказ не найден."
                ],
            ])->setStatusCode(404);
        }

        $order->update($data);
        $order->save();

        return response()->json([
            'code'      => 201,
            'message'   => "Данные заказа №{$order->id} изменены.",
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ])->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (empty($order)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Заказ не найден."
                ],
            ])->setStatusCode(404);
        }

        if ($order->order_status_id != 3 || $order->order_status_id != 4) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Незавершенный заказ удалить нельзя."
                ],
            ])->setStatusCode(404);
        }

        $order->delete();

        return response()->json([
            'code'      => 200,
            'message'   => "Заказ №{$id} был удален.",
        ]);
    }
}
