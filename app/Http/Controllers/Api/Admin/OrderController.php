<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Orders\OrderOperatorChangeRequest;
use App\Http\Requests\Orders\OrderSearchRequest;
use App\Http\Requests\Orders\OrderUpdateRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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

    public function setOperatorToOrder(Request $request, $orderId)
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

        if (! empty($order->operator_id)) {
            return response()->json([
                'error' => [
                    'code'      => 403,
                    'message'   => "Заказ уже обрабатывается другим оператором."
                ],
            ])->setStatusCode(403);
        }

        $user = $request->user();

        if (! $user->can('set operator')) {
            return response()->json([
                'error' => [
                    'code'      => 403,
                    'message'   => "Вы не являетесь оператором и не можете принять заказ."
                ],
            ])->setStatusCode(403);
        }

        $order->operator_id = $user->id;
        $order->save();

        return response()->json([
            'code'      => 200,
            'message'   => "Оператор назначен для заказа."
        ]);
    }

    public function unsetOperatorToOrder(Request $request, $orderId)
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

        if (empty($order->operator_id)) {
            return response()->json([
                'error' => [
                    'code'      => 403,
                    'message'   => "Заказ никем не обрабатывается."
                ],
            ])->setStatusCode(403);
        }

        $user = $request->user();

        if ($order->operator_id != $user->id && ! $user->hasRole('admin')) {
            return response()->json([
                'error' => [
                    'code'      => 403,
                    'message'   => "Вы не можете снять оператора, с чужого заказа."
                ],
            ])->setStatusCode(403);
        }

        if (($order->order_status_id != 3 || $order->order_status_id != 4) && ! $user->hasRole('admin')) {
            return response()->json([
                'error' => [
                    'code'      => 422,
                    'message'   => "Вы не можете сняться с заказа, пока он не завершен."
                ],
            ])->setStatusCode(422);
        }

        if (! $user->can('unset operator') && ! $user->hasRole('admin')) {
            return response()->json([
                'error' => [
                    'code'      => 403,
                    'message'   => "Вы не являетесь оператором и не можете сняться с заказа."
                ],
            ])->setStatusCode(403);
        }

        $order->operator_id = null;
        $order->save();

        return response()->json([
            'code'      => 200,
            'message'   => "Оператор снят с заказа."
        ]);
    }

    public function changeOrderOperator(OrderOperatorChangeRequest $request, $orderId)
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

        $operator = User::find($request->operator_id);

        if (empty($operator)) {
            return response()->json([
                'error' => [
                    'code'      => 404,
                    'message'   => "Оператор не найден."
                ],
            ])->setStatusCode(404);
        }

        $order->operator_id = $operator->id;
        $order->save();

        return response()->json([
            'code'      => 200,
            'message'   => "Оператор заказа изменен."
        ]);
    }
}
