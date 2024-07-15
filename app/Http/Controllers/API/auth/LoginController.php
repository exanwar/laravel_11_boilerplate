<?php

namespace App\Http\Controllers\API\auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt([
            'email'     =>  $request->email,
            'password'  =>  $request->password])){
            $user = Auth::user();
            $success['token']   =   $user->createToken('MyApp')->accessToken;
            $success['user']    =   $user;

            return $this->sendResponse($success, 'User login Successfully');
        } else {
            return $this->sendError('Unauthorised', ['error'=>'Unauthorised']);
        }
    }
}
