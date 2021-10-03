<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Requests\Orders\OrderOperatorChangeRequest;
use App\Http\Requests\Orders\OrderSearchRequest;
use App\Http\Requests\Orders\OrderUpdateRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderProductResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends AdminBaseController
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

        $data = [
            'orders'    => OrderCollection::make($orders),
        ];

        return $this->successResponse(200, null, $data);
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
            return $this->errorResponse(404, "Заказ не найден.");
        }

        $order->update($data);
        $order->save();

        $data = [
            'order'     => OrderResource::make($order),
            'products'  => ProductResource::collection($order->products),
        ];

        return $this->successResponse(201, "Данные заказа №{$order->id} изменены.", $data);
    }

    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (empty($order)) {
            return $this->errorResponse(404, "Заказ не найден.");
        }

        $data = [
            'order'     => OrderResource::make($order),
            'products'  => OrderProductResource::collection($order->products),
        ];

        return $this->successResponse(200, null, $data);
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
            return $this->errorResponse(404, "Заказ не найден.");
        }

        if (! ($order->order_status_id != 3 || $order->order_status_id != 4)) {
            return $this->errorResponse(422, "Незавершенный заказ удалить нельзя.");
        }

        $order->delete();

        return $this->successResponse(200, "Заказ №{$id} был удален.");
    }

    public function setOperatorToOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (empty($order)) {
            return $this->errorResponse(404, "Заказ не найден.");
        }

        if (! empty($order->operator_id)) {
            return $this->errorResponse(403, "Заказ уже обрабатывается другим оператором.");
        }

        $user = $request->user();

        if (! $user->can('set operator')) {
            return $this->errorResponse(403, "Вы не являетесь оператором и не можете принять заказ.");
        }

        $order->operator_id = $user->id;
        $order->save();

        return $this->successResponse(200, "Оператор назначен для заказа.");
    }

    public function unsetOperatorToOrder(Request $request, $orderId)
    {
        $order = Order::find($orderId);

        if (empty($order)) {
            return $this->errorResponse(404, "Заказ не найден.");
        }

        if (empty($order->operator_id)) {
            return $this->errorResponse(403, "Заказ никем не обрабатывается.");
        }

        $user = $request->user();

        if ($order->operator_id != $user->id && ! $user->hasRole('admin')) {
            return $this->errorResponse(403, "Вы не можете убрать оператора с чужого заказа.");
        }

        if (($order->order_status_id != 3 || $order->order_status_id != 4) && ! $user->hasRole('admin')) {
            return $this->errorResponse(400, "Вы не можете отказаться от заказа, пока он не завершён.");
        }

        if (! $user->can('unset operator') && ! $user->hasRole('admin')) {
            return $this->errorResponse(400, "Вы не являетесь оператором и не можете отказаться от заказа.");
        }

        $order->operator_id = null;
        $order->save();

        return $this->successResponse(200, "Оператор снят с заказа.");
    }

    public function changeOrderOperator(OrderOperatorChangeRequest $request, $orderId)
    {
        $order = Order::find($orderId);

        if (empty($order)) {
            return $this->errorResponse(404, "Заказ не найден.");
        }

        $operator = User::find($request->operator_id);

        if (empty($operator)) {
            return $this->errorResponse(404, "Оператор не найден.");
        }

        $order->operator_id = $operator->id;
        $order->save();

        return $this->successResponse(200, "Оператор заказа изменен.");
    }
}
