<?php

use App\User;
use Illuminate\Database\Seeder;
use jeremykenedy\LaravelRoles\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Add Roles
         *
         */
        if (Role::where('name', '=', 'Admin')->first() === null) {
            $adminRole = Role::create([
                'name'        => 'Admin',
                'slug'        => 'admin',
                'description' => 'Admin Role',
                'level'       => 5,
            ]);
        }

        if (Role::where('name', '=', 'Tsr')->first() === null) {
            $tsrRole = Role::create([
                'name'        => 'Tsr',
                'slug'        => 'tsr',
                'description' => 'Tsr Role',
                'level'       => 0,
            ]);
        }

        if (Role::where('name', '=', 'User')->first() === null) {
            $userRole = Role::create([
                'name'        => 'User',
                'slug'        => 'user',
                'description' => 'User Role',
                'level'       => 1,
            ]);
        }

        if (Role::where('name', '=', 'Ap')->first() === null) {
            $userRole = Role::create([
                'name'        => 'Ap',
                'slug'        => 'ap',
                'description' => 'Ap Role',
                'level'       => 2,
            ]);
        }
    }
}
