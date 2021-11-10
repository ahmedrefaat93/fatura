<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Traits\RespondsWithHttpStatus;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     *
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * description: login api
     ***********************************************************
     * endpoint: api/auth/login
     * method type: Post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type  => application/json
     * Accept        => application/json
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************
     * 'email'     => 'string'  required
     * 'password'  => 'string'  required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 422 => validation error
     * 401 => Unauthorized
     * 200 => logged Successfully
     * 500 => Server Error
     */
    public function login(LoginRequest $request){
        try {
            $credentials = $request->only('email', 'password');
            if (!$token = auth()->attempt($credentials)) {
                return $this->failure('Unauthorized', 401);
            }
            return $this->createNewToken($token);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }

    /**
     * description: register api
     ***********************************************************
     * endpoint: api/auth/register
     * method type: Post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type  => application/json
     * Accept        => application/json
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************
     * 'name'                  => 'string'  required
     * 'email'                 => 'string'  required
     * 'password'              => 'string'  required
     * 'password_confirmation' => 'string'  required
     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 400 => validation error
     * 401 => Unauthorized
     * 201 => register Successfully
     * 500 => Server Error
     */
    public function register(RegisterRequest $request) {
        try {
            $credentials = $request->only('name', 'email', 'password');
            $user = User::create(array_merge(
                $credentials,
                ['password' => bcrypt($request->password)]
            ));
            return $this->success('User successfully registered', $user, 201);
        }catch (\Exception $exception) {
            Log::channel("stack")->info("An Exception occurred in file "
                . $exception->getFile() . " and the message is "
                . $exception->getMessage()
                . " at line " . $exception->getLine());
            return $this->failure("Server Error",500);
        }
    }


    /**
     * description: logout api
     ***********************************************************
     * endpoint: api/auth/logout
     * method type: Post
     ************************************************************
     * Headers:
     ************************************************************
     * Content-Type   => application/json
     * Accept         => application/json
     * Authorization  => Bearer 'token'
     *************************************************************
     ***********************************************************
     * Inputs
     ***********************************************************

     ***********************************************************
     * Response Codes:
     ***********************************************************
     * 401 => Unauthorized
     * 200 => logout Successfully
     * 500 => Server Error
     */
    public function logout() {
        auth()->logout();
        return $this->success('User successfully signed out',[],200);
    }


    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return $this->success('Logged Successfully',[
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ],200);
    }
}
