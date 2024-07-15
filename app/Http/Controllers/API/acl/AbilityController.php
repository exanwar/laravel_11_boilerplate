<?php

namespace App\Http\Controllers\API\acl;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbilityController extends Controller
{
    public function index(){
        return auth()->user()->roles()->with('permissions')->get()
            ->pluck('permissions')
            ->flatten()
            ->pluck('slug')
            ->toArray();
    }
}