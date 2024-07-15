<?php

namespace App\Http\Controllers\API\controller;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\acl\RoleResource;
use App\Http\Resources\resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UsersController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = UserResource::collection(User::with('roles')->get());
        return $this->sendResponse($users, 'Users data has been downloaded!');
    }

    public function findUser()
    {
        if ($search = \Request::get('q')) {
            $users = User::with('roles')->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                    ->orWhere('email', 'LIKE', "%$search%");
            })
                ->get();
        }

        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['name'] = $request->full_name;
        $request['full_name'] = null;
        $request['email'] = $request->email_address;
        $request['email_address'] = null;
        $validator = Validator::make($request->all(),
            [
                'name' => 'required',
                'email' => 'required|unique:users',
            ],
            [
                'name.required' => 'The full name field is required',
                'email.required' => 'The email address field is required',
                'email.unique' => 'The email address has already been there.'
            ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input['name'] = $request->name;
        $input['email'] = $request->email;
        $input['password'] = $request->password;
        $user = User::create($input);
        $user->roles()->sync($request->input('roles'), []);
        $success['User'] = [
            'full_name' => $user->name,
            'email_address' => $user->email,
            'roles' => RoleResource::collection($user->roles()->with('permissions')->get())
        ];

        return $this->sendResponse($success, 'Permission has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            $user = [
                'full_name' => $user->name,
                'email_address' => $user->email,
                'roles' => RoleResource::collection($user->roles()->with('permissions')->get())
            ];
            return $this->sendResponse($user, 'User details has been drawn successfully!');
        } else {
            return $this->sendError('Validation Error', 'Something went wrong!');
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $gotUser = User::find($id);

        if ($gotUser) {
            $request['name'] = $request->full_name;
            $request['full_name'] = null;
            $request['email'] = $request->email_address;
            $request['email_address'] = null;
            $validator = Validator::make($request->all(),
                [
                    'name' => '',
                    'email' => 'unique:users,email,' . $id
                ],
                [
                    'name.required' => 'The full name field is required',
                    'email.required' => 'The email address field is required',
                    'email.unique' => 'The email address has already been there.'
                ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $input['name'] = $request->name ? $request->name : $gotUser->name;
            $input['email'] = $request->email ? $request->email : $gotUser->email;

            $user = $gotUser->update([
                'name' => $input['name'],
                'email' => $input['email'],
            ]);
            $gotUser->roles()->sync($request->input('roles'), []);
            return $this->sendResponse($user, 'User details has been updated successfully');
        } else {
            return $this->sendError('Validation Error', 'Something went wrong!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->forceDelete();
            return $this->sendResponse("Deleted", 'User has been deleted successfully');
        } else return $this->sendError('Validation Error', 'Something went wrong!');
    }
}
