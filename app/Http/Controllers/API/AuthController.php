<?php
   
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;

   
class AuthController extends BaseController
{

    /**
     * User Login
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $auth = Auth::user(); 
            $success['token'] =  $auth->createToken('LaravelSanctumAuth')->plainTextToken; 
            $success['first_name'] =  $auth->first_name;
            $success['last_name'] =  $auth->last_name;
   
            return $this->successResponse($success, 'User logged-in!');
        } 
        else{ 
            return $this->errorResponse('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    /**
     * User Register
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->errorResponse($validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('LaravelSanctumAuth')->plainTextToken;
        $success['first_name'] =  $user->first_name;
        $success['last_name'] =  $user->last_name;
   
        return $this->successResponse($success, 'User successfully registered!');
    }
   
}