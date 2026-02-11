<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Magasin;
use App\Models\Boutique;
use App\Models\Produit;
use App\Models\Fournisseur;
use App\Models\Partenaire;
use App\Models\StockMagasin;
use App\Models\StockBoutique;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Créer les utilisateurs
        $admin = User::create([
            'name' => 'Admin Système',
            'email' => 'admin@gestion.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $gestionnaire = User::create([
            'name' => 'Jean Gestionnaire',
            'email' => 'gestionnaire@gestion.com',
            'password' => Hash::make('password'),
            'role' => 'gestionnaire',
        ]);

        $vendeur = User::create([
            'name' => 'Marie Vendeuse',
            'email' => 'vendeur@gestion.com',
            'password' => Hash::make('password'),
            'role' => 'vendeur',
        ]);

        // Créer les magasins
        $magasin = Magasin::create([
            'nom' => 'Magasin Central',
            'localisation' => 'Abidjan, Cocody',
            'responsable_id' => $gestionnaire->id,
        ]);

        // Créer les boutiques
        $boutique1 = Boutique::create([
            'nom' => 'Boutique Plateau',
            'localisation' => 'Abidjan, Plateau',
            'magasin_id' => $magasin->id,
        ]);

        $boutique2 = Boutique::create([
            'nom' => 'Boutique Yopougon',
            'localisation' => 'Abidjan, Yopougon',
            'magasin_id' => $magasin->id,
        ]);

        // Créer les fournisseurs
        $fournisseur1 = Fournisseur::create([
            'nom' => 'Fournisseur A',
            'contact' => 'M. Koné',
            'telephone' => '+225 01 23 45 67 89',
            'email' => 'contact@fournisseur-a.com',
        ]);

        $fournisseur2 = Fournisseur::create([
            'nom' => 'Fournisseur B',
            'contact' => 'Mme Touré',
            'telephone' => '+225 02 34 56 78 90',
            'email' => 'contact@fournisseur-b.com',
        ]);

        // Créer les partenaires
        $partenaire1 = Partenaire::create([
            'nom' => 'Partenaire X',
            'contact' => 'M. Bamba',
            'telephone' => '+225 03 45 67 89 01',
            'type_accord' => 'achat',
        ]);

        // Créer les produits
        $produits = [
            [
                'nom' => 'T-Shirt Homme',
                'categorie' => 'Vêtements',
                'description' => 'T-shirt en coton de qualité',
                'prix_achat' => 3000,
                'prix_vente' => 5000,
                'statut' => 'actif',
            ],
            [
                'nom' => 'Jean Femme',
                'categorie' => 'Vêtements',
                'description' => 'Jean moderne pour femme',
                'prix_achat' => 7000,
                'prix_vente' => 12000,
                'statut' => 'actif',
            ],
            [
                'nom' => 'Chaussures Sport',
                'categorie' => 'Chaussures',
                'description' => 'Chaussures de sport confortables',
                'prix_achat' => 8000,
                'prix_vente' => 15000,
                'statut' => 'actif',
            ],
            [
                'nom' => 'Sac à Main',
                'categorie' => 'Accessoires',
                'description' => 'Sac à main élégant',
                'prix_achat' => 5000,
                'prix_vente' => 9000,
                'statut' => 'actif',
            ],
        ];

        $produitIds = [];
        foreach ($produits as $produit) {
            $p = Produit::create($produit);
            $produitIds[] = $p->id;
        }

        // Créer les stocks dans le magasin
        foreach ($produitIds as $produitId) {
            StockMagasin::create([
                'magasin_id' => $magasin->id,
                'produit_id' => $produitId,
                'quantite' => rand(50, 200),
                'seuil_alerte' => 10,
            ]);
        }

        // Créer les stocks dans les boutiques
        foreach ($produitIds as $produitId) {
            StockBoutique::create([
                'boutique_id' => $boutique1->id,
                'produit_id' => $produitId,
                'quantite' => rand(10, 50),
                'seuil_alerte' => 5,
            ]);

            StockBoutique::create([
                'boutique_id' => $boutique2->id,
                'produit_id' => $produitId,
                'quantite' => rand(10, 50),
                'seuil_alerte' => 5,
            ]);
        }

        $this->command->info('Données de test créées avec succès!');
        $this->command->info('Comptes créés:');
        $this->command->info('Admin: admin@gestion.com / password');
        $this->command->info('Gestionnaire: gestionnaire@gestion.com / password');
        $this->command->info('Vendeur: vendeur@gestion.com / password');
    }
}
