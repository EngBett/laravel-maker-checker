<?php

namespace App\Helpers;

use App\Models\User;

class Functions
{
    public static function IsAdmin(User $user){
        $admin_role_name = env('ADMIN_ROLE', 'admin');
        foreach ($user->roles as $role){
            if($role->name==$admin_role_name) return true;
        }
        return false;
    }
}
