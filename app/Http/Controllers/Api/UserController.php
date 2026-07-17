<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(){
        $user= $this->userService->getAll();
        return UserResource::collection($user);
    }


    public function store(StoreUserRequest $request){
        $user = $this->userService->create($request->validated());

        return new UserResource($user);
    }

    public function show(User $user){
        $user = $this->userService->getDetails($user);
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $user){

        $user=$this->userService->update($user,$request->validated());

        return new UserResource($user);
    }

    public function destroy(User $user){
        $user = $this->userService->delete($user);

        return new UserResource($user);
    }
    public function changePassword(Request $request, User $user){
        $request->validate([
            'password'=>['required','string','min:8']
        ]);
        $this->userService->changePassword($user,$request->input('password'));

        return response()->json(['message'=>'Password changed successfully']);
    }
}
