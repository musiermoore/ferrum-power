<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;

class OrderStatusController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $statuses = OrderStatus::all();

        return response()->json([
            'code'      => 200,
            'product'  => OrderStatusResource::collection($statuses),
        ]);
    }
}
