<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
{
    $credentials = $request->validated();

    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    return response()->json([
        'token' => $token,
        'user' => new UserResource(auth('api')->user())
    ]);
}

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message'=>'Logged out'
        ]);
    }

    public function register(StoreUserRequest $request){
        $data = $request->validated();
        $user = User::create([
            'name'=>$data['name'],
            'email'=>$data['email'],
            'role_id'=>$data['role_id'],
            'password'=> Hash::make($data['password']),
            'image_url'=>$data['image_url'],
            'phone'=>$data['phone'],
            'date_of_birth'=>$data['date_of_birth'],
            'address'=>$data['address'],
            'is_active'=>true
        ]);
        return new UserResource($user);
    }

    public function updateProfile(UpdateUserRequest $request){
        $user = $request->user();
        $user->update($request->validated());
        $user->refresh();
        return new UserResource($user);
    }

    public function changePassword(ChangePasswordRequest $request){
        $user = $request->user();

        $data = $request->validated();

        if(!Hash::check($data['password'],$user->password)){
            return response()->json([
                "message"=>"incorrect password"
            ],422);
        }

        $user->update([
            'password'=>Hash::make($data['newPassword'])
        ]);
        return response()->json(
            [
                'message'=>'password updated successfully'
            ],204
        );
       
            
        

    }

    public function profile(){
        $user = auth('api')->user();
        return new UserResource($user);
    }

}
