<?php

namespace App\Http\Resources\acl;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'role_id'       =>  $this->id,
            'role'          =>  $this->title,
            'role_slug'     => $this->slug,
            'permissions'   =>  PermissionResource::collection($this->whenLoaded('permissions'))
        ];
    }
}
