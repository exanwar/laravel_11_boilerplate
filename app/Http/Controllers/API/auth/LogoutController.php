<?php

namespace App\Http\Controllers\API\auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends BaseController
{
    /**
     * Logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(): JsonResponse
    {
        $success = Auth::logout();
        $success['user'] = Auth()->user();

        return $this->sendResponse($success, 'User Logged out Successfully');
    }
}
