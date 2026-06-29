<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('AdminUserSeeder ignoré : autorisé uniquement en local/testing.');
            return;
        }

        $email = env('ADMIN_SEED_EMAIL', 'admin@admin.com');
        $password = env('ADMIN_SEED_PASSWORD', Str::random(16));

        User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Administrateur',
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );

        if (! env('ADMIN_SEED_PASSWORD')) {
            $this->command?->warn("Mot de passe admin généré aléatoirement : {$password}");
        }
    }
}
