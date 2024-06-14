<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'luis',
                'email' => 'luis.vazquez.franco.al@iespoligonosur.org',
                'user_name' => 'luisvaz',
                'password' => Hash::make('admin1')
            ],
        ];

         foreach ($users as $user) {
            $createdUser = User::create($user);
            $token = $createdUser->createToken('API Token')->plainTextToken;
            echo "Token for {$createdUser->name}: $token\n";
        } 
    }
}
