<?php

namespace App\Http\Controllers\API\Common;

use App\Http\Controllers\Controller;
use App\Http\Resources\Common\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function info(){
        $id = auth('api')->user()->id;
        $user = User::with('shop', 'roles')->where('id', $id)->get();
        return UserResource::collection($user);
    }
}