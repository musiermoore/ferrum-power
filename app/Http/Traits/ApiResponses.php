<?php

namespace App\Http\Traits;

trait ApiResponses
{
    public function validationErrorResponse()
    {

    }

    public function errorResponse($code, $message)
    {
        $response = [
            'status' => 'error',
            'code' => $code,
            'message' => $message
        ];

        return response()->json($response)->setStatusCode($code);
    }

    public function successResponse($code = 200, $message = null, $data = null)
    {
        $response = [
            'status' => 'success',
            'code' => $code,
        ];

        if (!empty($message)) {
            $response['message'] = $message;
        }

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response)->setStatusCode($code);
    }
}
