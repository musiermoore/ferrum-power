<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'patronymic' => $this->patronymic,
            'login' => $this->login,
            'roles' => $this->getRoleNames(), // spatie's method
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
