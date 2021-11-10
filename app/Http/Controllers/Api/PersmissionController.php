<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddPermissionRequest;
use App\Http\Requests\Api\AssignPermissionsRequest;
use App\Http\Requests\Api\CheckAuthedPermissionRequest;
use App\Http\Requests\Api\CheckPermissionRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use App\Http\Traits\RespondsWithHttpStatus;
class PersmissionController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * description: list all permissions api
     ***********************************************************
     * endpoint: api/permissions
     * method type: get
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type   => application/json
     * Accept         => application/json
     * Authorization  => Bearer 'token' for super_admin
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************

     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 200 => returned Successfully
     * 500 => Server Error
     */
    public function index(){
        try {
            $permissions = Permission::all();
            return $this->success('', $permissions, 200);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }

    }

    /**
     * description: create new permission api
     ***********************************************************
     * endpoint: api/permission/create
     * method type: post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type   => application/json
     * Accept         => application/json
     * Authorization  => Bearer 'token' for super_admin
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************
     * 'name' => 'string' required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 201 => created Successfully
     * 500 => Server Error
     */
    public function store(AddPermissionRequest $request){
        try {
            $permission = Permission::create(['name' => $request->name]);
            return $this->success('Permission Created Successfully', $permission, 201);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description: assign permissions for user api
     ***********************************************************
     * endpoint: api/permission/assign
     * method type: post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type   => application/json
     * Accept         => application/json
     * Authorization  => Bearer 'token' for super_admin
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************
     * 'user_id'    => 'number'  required
     * 'permissions => 'array of permission ids' required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 200 => assigned Successfully
     * 500 => Server Error
     */
    public function assign(AssignPermissionsRequest $request){
        try {
            $user = User::find($request->user_id);
            $user->givePermissionTo($request->input('permissions'));
            return $this->success('Assign Permissions to User Successfully', $user, 200);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description: super admin check a permission for user api
     ***********************************************************
     * endpoint: api/permission/can
     * method type: post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type   => application/json
     * Accept         => application/json
     * Authorization  => Bearer 'token' for super_admin
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************
     * 'user_id'   => 'number'  required
     * 'permission => 'permission id number'  required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 403 => forbidden this user not has this permission
     * 200 => user his this permission
     * 500 => Server Error
     */
    public function check(CheckPermissionRequest $request){
        try {
            $user = User::find($request->user_id);
            if ($user->hasPermissionTo($request->permission)) {
                return $this->success('User has this permission', [], 200);
            }
            return $this->failure("User Not has this permission", 403);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description:  check a permission for logged user api
     ***********************************************************
     * endpoint: api/authed/permission/can
     * method type: post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type   => application/json
     * Accept         => application/json
     * Authorization  => Bearer 'token' for logged user
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************
     * 'permission => 'permission id number'  required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 403 => forbidden logged user not has this permission
     * 200 => logged user his this permission
     * 500 => Server Error
     */
    public function checkForAuthorize(CheckAuthedPermissionRequest $request){
        try {
            $user = auth()->user();
            if ($user->hasPermissionTo($request->permission)) {
                return $this->success('User has this permission', [], 200);
            }
            return $this->failure("User Not has this permission", 403);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }
}
