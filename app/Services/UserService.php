<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService{
    public function create(array $data){
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
        return $user;
    }

    public function getAll(){
        return User::with('role')->orderBy('id')->get();
    }


    public function getDetails(User $user): User{
        return $user->load('role');
    }

    public function update(User $user, array $data) :User{
        $user->update($data);
        return $user->load('role');
    }
    public function delete(User $user){
        if($user->id == auth()->id()){
            throw new \Exception('You cannot deactivate yourself.');
        }
        $user->update([
            'is_active'=>false
        ]);
        $user->delete();
        return $user->load('role');
    }

    public function changePassword(User $user,string $password): void {

        $user->update([
        'password' => Hash::make($password)
    ]);
}


}