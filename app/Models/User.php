<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\MustVerifyEmail;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, HasRoles, MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_name',
        'email',
        'first_name',
        'last_name',
        'other_name',
        'phone_no',
        'avatar',
        'is_2fa_enabled',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    /**
     * Set the user's role by detaching existing roles and assigning a new role.
     *
     * @param string $roleName The name of the role to assign to the user.
     */
    public function setRole(string $roleName)
    {
        if ($this->roles()->exists()) {
            $this->roles()->detach();
        }

        $this->assignRole($roleName);
    }

    /**
     * Get all permission names assigned to the user.
     *
     * @return array
     */
    public function getAllPermissionNames(): array
    {
        return $this->getAllPermissions()->pluck('name')->toArray();
    }
}
