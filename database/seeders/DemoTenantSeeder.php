<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un tenant de démonstration
        $tenant = Tenant::create([
            'name' => 'Entreprise Démo',
            'slug' => 'entreprise-demo-' . Str::random(6),
            'email' => 'demo@entreprise.com',
            'phone' => '+33 6 12 34 56 78',
            'address' => '123 Rue de la Démo',
            'city' => 'Paris',
            'country' => 'France',
            'postal_code' => '75001',
            'plan' => 'pro',
            'is_active' => true,
            'trial_ends_at' => now()->addDays(30),
            'subscription_ends_at' => now()->addDays(30),
        ]);

        // Créer un utilisateur admin pour ce tenant
        $user = User::create([
            'name' => 'Admin Démo',
            'email' => 'admin@entreprise.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'tenant_id' => $tenant->id,
        ]);

        // Créer un utilisateur gestionnaire pour ce tenant
        $gestionnaire = User::create([
            'name' => 'Gestionnaire Démo',
            'email' => 'gestionnaire@entreprise.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
            'tenant_id' => $tenant->id,
        ]);

        // Créer un utilisateur vendeur pour ce tenant
        $vendeur = User::create([
            'name' => 'Vendeur Démo',
            'email' => 'vendeur@entreprise.com',
            'password' => Hash::make('password'),
            'role' => 'vendeur',
            'tenant_id' => $tenant->id,
        ]);

        $this->command->info('Tenant de démonstration créé avec succès!');
        $this->command->info('Email admin: admin@entreprise.com');
        $this->command->info('Mot de passe: password');
        $this->command->info('Email gestionnaire: gestionnaire@entreprise.com');
        $this->command->info('Email vendeur: vendeur@entreprise.com');
    }
}
