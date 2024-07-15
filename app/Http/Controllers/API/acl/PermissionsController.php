<?php

namespace App\Http\Controllers\API\acl;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\acl\permissions\PermissionCreateRequest;
use App\Http\Requests\Dev\PermissionRequest;
use App\Http\Resources\acl\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class PermissionsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = PermissionResource::collection(Permission::all());
        return $this->sendResponse($permissions, "Permissions has been downloaded");
    }

    public function findPermission()
    {
        if ($search = \Request::get('q')) {
            $permissions = Permission::where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%$search%")
                    ->orWhere('slug', 'LIKE', "%$search%");
            })
                ->get();
        }

        return PermissionResource::collection($permissions);
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
        $request['title'] = $request->permission;
        $request['permission'] = null;
        $validator = Validator::make($request->all(),
            [
                'title' => 'required|unique:permissions',
            ],
            [
                'title.required' => 'The permission field is required',
                'title.unique' => 'The permission has already been there.'
            ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $input['title'] = $request->title;
        $input['slug'] = Str::of($request->title)->replace(" ", "_")->lower();
        $permission = Permission::create($input);
        $success['permission'] = [
            'permission_id' => $permission->id,
            'permission_name' => $permission->title,
            'permission_slug' => $permission->slug
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
        $permission = Permission::where('id', $id)->first();
        if($permission){

            return $this->sendResponse(PermissionResource::make($permission), 'Permission has been drawn successfully!');
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
        $gotPermission = Permission::find($id);

        if($gotPermission){
        $request['title'] = $request->permission;
        $request['permission'] = null;
        $validator = Validator::make($request->all(),
            [
                'title' => 'required|unique:permissions,title,' . $id
            ],
            [
                'title.required' => 'The permission field is required',
                'title.unique' => 'The permission has already been there.'
            ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors());
        }

        $permission = $gotPermission->update([
            'title' =>  $request->title,
            'slug'  =>  Str::of($request->title)->replace(" ", "_")->lower()
        ]);
        return $this->sendResponse($permission, 'Permission has been updated successfully');}
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
        $permission = Permission::find($id);
        if($permission){
        $permission->forceDelete();
        return $this->sendResponse("Deleted", 'Permission has been deleted successfully');}
        else return $this->sendError('Validation Error', 'Something went wrong!');
    }
}