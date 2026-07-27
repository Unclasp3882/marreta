<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final class AdminUserSeeder extends Seeder
{
    /**
     * Runs on every container start, so it must never overwrite an existing
     * account — the password may have been changed from the admin panel.
     */
    public function run(): void
    {
        $password = config('marreta.admin.password');

        if (blank($password)) {
            $message = 'AdminUserSeeder: ADMIN_PASSWORD is not set, skipping admin user creation.';

            Log::warning($message);
            $this->command?->warn($message);

            return;
        }

        User::firstOrCreate(
            ['email' => config('marreta.admin.email')],
            [
                'name' => 'Administrador',
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );
    }
}
