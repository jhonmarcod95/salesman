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
        if (Role::where('name', '=', 'It')->first() === null) {
            $itRole = Role::create([
                'name'        => 'It',
                'slug'        => 'it',
                'description' => 'It Role',
                'level'       => 9,
            ]);
        }

        if (Role::where('name', '=', 'President')->first() === null) {
            $presidentRole = Role::create([
                'name'        => 'President',
                'slug'        => 'president',
                'description' => 'President Role',
                'level'       => 8,
            ]);
        }

        if (Role::where('name', '=', 'Evp')->first() === null) {
            $evpRole = Role::create([
                'name'        => 'Evp',
                'slug'        => 'evp',
                'description' => 'Evp Role',
                'level'       => 7,
            ]);
        }

        if (Role::where('name', '=', 'Vp')->first() === null) {
            $vpRole = Role::create([
                'name'        => 'Vp',
                'slug'        => 'vp',
                'description' => 'Vp Role',
                'level'       => 6,
            ]);
        }

        if (Role::where('name', '=', 'Avp')->first() === null) {
            $avpRole = Role::create([
                'name'        => 'Avp',
                'slug'        => 'avp',
                'description' => 'Avp Role',
                'level'       => 5,
            ]);
        }

        if (Role::where('name', '=', 'Coordinator')->first() === null) {
            $apRole = Role::create([
                'name'        => 'Coordinator',
                'slug'        => 'coordinator',
                'description' => 'Coordinator Role',
                'level'       => 4,
            ]);
        }

        if (Role::where('name', '=', 'Manager')->first() === null) {
            $managerRole = Role::create([
                'name'        => 'Manager',
                'slug'        => 'manager', 
                'description' => 'Manager Role',
                'level'       => 3,
            ]);
        }

        if (Role::where('name', '=', 'Ap')->first() === null) {
            $apRole = Role::create([
                'name'        => 'Ap',
                'slug'        => 'ap',
                'description' => 'Ap Role',
                'level'       => 2,
            ]);
        }

        if (Role::where('name', '=', 'Tsr')->first() === null) {
            $tsrRole = Role::create([
                'name'        => 'Tsr',
                'slug'        => 'tsr',
                'description' => 'Tsr Role',
                'level'       => 1,
            ]);
        }

        if (Role::where('name', '=', 'Hr')->first() === null) {
            $hrRole = Role::create([
                'name'        => 'Hr',
                'slug'        => 'hr',
                'description' => 'Hr Role',
                'level'       => 0,
            ]);
        }

    }
}
