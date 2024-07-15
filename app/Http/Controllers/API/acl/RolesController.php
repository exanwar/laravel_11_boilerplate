<?php

namespace App\Http\Controllers\API\acl;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\acl\RoleResource;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class RolesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = RoleResource::collection(Role::with('permissions')->orderBy('id','desc')->get());
        return $this->sendResponse($roles, "Roles has been downloaded");
    }

    public function findRole()
    {
        if ($search = \Request::get('q')) {
            $roles = Role::where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('slug', 'LIKE', "%$search%");
            })
                ->with('permissions')->orderBy('id', 'desc')->get();
        }

        return RoleResource::collection($roles);
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
        $request['title'] = $request->role;
        $request['role'] = null;
        $validator = Validator::make($request->all(),
            [
                'title' => 'required|unique:roles',
            ],
            [
                'title.required' => 'The Role field is required',
                'title.unique' => 'The Role has already been there.'
            ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input['title'] = $request->title;
        $input['slug'] = Str::of($request->title)->replace(" ", "_")->lower();
        $role = Role::create($input);
        $success['role'] = [
            'Role_id' => $role->id,
            'Role_name' => $role->title,
            'Role_slug' => $role->slug
        ];
        $success['permission'] = $role->permissions()->sync($request->input("permissions"), []);
        $success['users'] = $role->users()->sync($request->input('users'), []);

        return $this->sendResponse($success, 'Role has been created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::with('permissions')->where('id', $id)->first();
        if($role){

            return $this->sendResponse(RoleResource::make($role), 'Role has been drawn successfully!');
        }else{
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
        $gotRole = Role::find($id);

        if($gotRole){
            $request['title'] = $request->role;
            $request['role'] = null;
            $validator = Validator::make($request->all(),
                [
                    'title' => 'required|unique:roles,title,' . $id
                ],
                [
                    'title.required' => 'The Role field is required',
                    'title.unique' => 'The Role has already been there.'
                ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error', $validator->errors());
            }

            $role = $gotRole->update([
                'title' =>  $request->title,
                'slug'  =>  Str::of($request->title)->replace(" ", "_")->lower()
            ]);

            $gotRole->permissions()->sync($request->input('permissions', []));
            return $this->sendResponse($role, 'Role has been updated successfully');}
        else{
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
        $role = Role::find($id);
        if($role){
            $role->forceDelete();
            return $this->sendResponse("Deleted", 'Role has been deleted successfully');}
        else return $this->sendError('Validation Error', 'Something went wrong!');
    }
}