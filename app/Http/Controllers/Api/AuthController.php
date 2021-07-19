<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $data["password"] = bcrypt($data["password"]);

        $user = User::create($data);
        $user->assignRole($data["role"]);

        return response()->json([
            'code'      => 201,
            'message'   => "Пользователь создан.",
            'user'      => UserResource::make($user),
        ])->setStatusCode(201);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->all();

        $user = User::where('login', $data['login'])->first();

        if ( !$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'error' => [
                    'code'       => 401,
                    'message'    => "Такого пользователя не существует или введен неверный пароль.",
                ],
            ], 401);
        }

        return response()->json([
            'code'      => 200,
            'message'   => "Вы успешно вошли в систему.",
            'token'     => $user->createToken('Auth Token')->accessToken,
            'user'      => UserResource::make($user),
        ])->setStatusCode(200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }
}
