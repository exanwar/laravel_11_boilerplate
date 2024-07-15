<?php

namespace App\Http\Controllers\API\auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\auth\RegisterPostRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterPostRequest $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'          =>  'required',
            'email'         =>  'required|email',
            'password'      =>  'required',
            'c_password'    =>  'required|same:password'
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input              = $request->all();
        $input['password']  = bcrypt($input['password']);
        $user               = User::create($input);
        $success['token']   = $user->createToken('MyApp')->accessToken;
        $success['user']    =   $user;

        return $this->sendResponse($success, 'User register successfully');
    }
}
