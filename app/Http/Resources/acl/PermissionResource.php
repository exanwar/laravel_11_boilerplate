<?php

namespace App\Http\Resources\acl;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'permission_id'    =>  $this->id,
            'permission' =>  $this->title,
            'permission_slug' =>   $this->slug,
        ];
    }
}
