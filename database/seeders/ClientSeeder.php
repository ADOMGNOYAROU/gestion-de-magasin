<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'nom' => 'Dupont',
                'prenom' => 'Jean',
                'email' => 'jean.dupont@example.com',
                'telephone' => '+22890123456',
                'date_naissance' => '1990-05-15',
                'adresse' => '123 Rue de la Paix',
                'ville' => 'Lomé',
                'pays' => 'Togo',
                'code_postal' => '12345',
                'sexe' => 'M',
                'statut' => 'actif',
                'solde_points' => 50,
                'total_achats' => 150000,
            ],
            [
                'nom' => 'Koffi',
                'prenom' => 'Marie',
                'email' => 'marie.koffi@example.com',
                'telephone' => '+22890765432',
                'date_naissance' => '1985-08-20',
                'adresse' => '456 Avenue des Commerçants',
                'ville' => 'Lomé',
                'pays' => 'Togo',
                'code_postal' => '12345',
                'sexe' => 'F',
                'statut' => 'actif',
                'solde_points' => 75,
                'total_achats' => 200000,
            ],
            [
                'nom' => 'Tchagbalé',
                'prenom' => 'Paul',
                'email' => 'paul.tchagbale@example.com',
                'telephone' => '+22891234567',
                'date_naissance' => '1992-12-10',
                'adresse' => '789 Boulevard du Marché',
                'ville' => 'Kara',
                'pays' => 'Togo',
                'code_postal' => '23456',
                'sexe' => 'M',
                'statut' => 'actif',
                'solde_points' => 25,
                'total_achats' => 75000,
            ],
        ];

        foreach ($clients as $client) {
            Client::firstOrCreate([
                'email' => $client['email'],
            ], $client);
        }
    }
}
