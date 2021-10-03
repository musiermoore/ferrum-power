<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function getUser(Request $request)
    {
        $user = $request->user();

        $data = [
            'user' => UserResource::make($user),
        ];

        return $this->successResponse(200, "Пользователь создан.", $data);
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->all();
        $data["password"] = bcrypt($data["password"]);

        $user = User::create($data);
        $user->assignRole($data["role"]);

        $data = [
            'user' => UserResource::make($user),
        ];

        return $this->successResponse(201, "Пользователь создан.", $data);
    }

    public function login(LoginRequest $request)
    {
        $data = $request->all();

        $user = User::where('login', $data['login'])->first();

        if ( !$user || !Hash::check($data['password'], $user->password)) {
            return $this->errorResponse(401, "Такого пользователя не существует или введен неверный пароль.");
        }

        $token = $user->createToken('Auth Token')->accessToken;

        $data = [
            'token'     => $token,
            'user'      => UserResource::make($user),
        ];

        return $this->successResponse(200, "Вы успешно вошли в систему.", $data);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
    }

    public function check(Request $request)
    {
        $user = $request->user();

        $data = [
            'token' => $user->tokens()->orderByDesc('id')->first(),
        ];

        return $this->successResponse(200, null, $data);
    }
}
