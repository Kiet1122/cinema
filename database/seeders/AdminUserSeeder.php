<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Manager;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tạo User với vai trò Manager
        $user = User::create([
            'Email' => 'admin1@example.com',
            'Password' => Hash::make('123456'), // Mật khẩu an toàn
            'Role' => 'Manager',
            'Status' => 'Active',
        ]);

        // Tạo Manager và liên kết với User
        Manager::create([
            'UserID' => $user->UserID,
            'FullName' => 'Admin1 Manager',
            'Phone' => '0901234567',
            'Avatar' => null,
        ]);
    }
}