<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Override;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'password','role_id','is_active','address','phone','date_of_birth','image_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function role(){
        return $this->belongsTo(Role::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }

     public function hasRole(array|string $roles){
        $allowedRoles = (array) $roles;
        
        return $this->role &&
                in_array(
                    $this->role->name,
                    $allowedRoles
                );

    }
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    #[Override]
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    #[Override]
    public function getJWTCustomClaims()
    {
        return [
            'role'=>$this->role()
        ];
    }

    
}
