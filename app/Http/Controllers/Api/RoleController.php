<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AddRoleRequest;
use App\Http\Requests\Api\AssignRolesRequest;
use App\Http\Requests\Api\CheckAuthedRoleRequest;
use App\Http\Requests\Api\CheckRoleRequest;
use Spatie\Permission\Models\Role;
use App\Http\Traits\RespondsWithHttpStatus;
use App\Models\User;
class RoleController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * description: list all roles api
     ***********************************************************
     * endpoint: api/roles
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
            $roles = Role::all();
            return $this->success('', $roles, 200);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }

    }

    /**
     * description: create new role api
     ***********************************************************
     * endpoint: api/role/create
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
     * 'name'        => 'string' required
     * 'permissions' => 'array of permissions ids' required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 201 => created Successfully
     * 500 => Server Error
     */
    public function store(AddRoleRequest $request){
        try {
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permissions'));
            return $this->success('Role Create Sucessfully', $role, 201);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description: assign roles for user api
     ***********************************************************
     * endpoint: api/role/assign
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
     * 'roles => 'array of roles ids' required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 200 => assigned Successfully
     * 500 => Server Error
     */
    public function assign(AssignRolesRequest $request){
        try {
            $user = User::find($request->user_id);
            $user->assignRole($request->input('roles'));
            return $this->success('Assign roles to User Successfully', $user, 200);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description: super admin check a role for user api
     ***********************************************************
     * endpoint: api/role/can
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
     * 'role => 'role id number'  required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 403 => forbidden this user not has this role
     * 200 => user his this role
     * 500 => Server Error
     */
    public function check(CheckRoleRequest $request){
        try {
            $user = User::find($request->user_id);
            if ($user->hasRole($request->role)) {
                return $this->success('User has this role', [], 200);
            }
            return $this->failure("User Not has this role", 403);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description: check a role for logged user api
     ***********************************************************
     * endpoint: api/authed/role/can
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
     * 'role' => 'role id number'  required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 400 => validation Error
     * 403 => forbidden logged user not has this role
     * 200 => logged user his this role
     * 500 => Server Error
     */
    public function checkForAuthorize(CheckAuthedRoleRequest $request){
        try {
            $user = auth()->user();
            if ($user->hasRole($request->role)) {
                return $this->success('User has this role', [], 200);
            }
            return $this->failure("User Not has this role", 403);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }
}
