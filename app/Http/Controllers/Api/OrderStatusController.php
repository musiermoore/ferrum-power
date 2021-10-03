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

        $data = [
            'statuses'  => OrderStatusResource::collection($statuses),
        ];

        return $this->successResponse(200, null, $data);
    }
}
