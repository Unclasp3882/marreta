<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => config('marreta.admin.email'),
            'password' => Hash::make(config('marreta.admin.password')),
            'email_verified_at' => now(),
        ]);
    }
}
