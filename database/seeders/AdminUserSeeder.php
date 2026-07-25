<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminUserSeeder extends Seeder
{
    /**
     * Runs on every container start, so it must never overwrite an existing
     * account — the password may have been changed from the admin panel.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => config('marreta.admin.email')],
            [
                'name' => 'Administrador',
                'password' => Hash::make(config('marreta.admin.password')),
                'email_verified_at' => now(),
            ],
        );
    }
}
