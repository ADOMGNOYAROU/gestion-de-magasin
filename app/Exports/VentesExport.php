<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VentesExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    protected $filters;
    protected $user;

    public function __construct($data, $filters, $user)
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->user = $user;
    }

    public function collection()
    {
        $collection = collect();

        // Ajouter les informations générales
        $collection->push(['RAPPORT DE VENTES']);
        $collection->push(['Période : ' . $this->filters['date_debut'] . ' au ' . $this->filters['date_fin']]);
        $collection->push(['Généré par : ' . $this->user->name . ' (' . $this->user->role . ')']);
        $collection->push(['Date de génération : ' . now()->format('d/m/Y H:i:s')]);
        $collection->push([]);

        // Ajouter les totaux
        $collection->push(['RÉSUMÉ']);
        $collection->push(['Total des ventes', $this->data['totalVentes']]);
        $collection->push(['Chiffre d\'affaires total', number_format($this->data['totalCA'], 0, ',', ' ') . ' FCFA']);
        $collection->push(['Bénéfice total', number_format($this->data['totalBenefice'], 0, ',', ' ') . ' FCFA']);
        $collection->push([]);

        // Ajouter les ventes par boutique
        $collection->push(['VENTES PAR BOUTIQUE']);
        $collection->push(['Boutique', 'Magasin', 'Nombre de ventes', 'CA', 'Bénéfice']);
        
        foreach ($this->data['ventesParBoutique'] as $boutiqueData) {
            $collection->push([
                $boutiqueData['boutique']->nom,
                $boutiqueData['boutique']->magasin->nom,
                $boutiqueData['ventes'],
                number_format($boutiqueData['ca'], 0, ',', ' ') . ' FCFA',
                number_format($boutiqueData['benefice'], 0, ',', ' ') . ' FCFA'
            ]);
        }
        $collection->push([]);

        // Ajouter les ventes par produit
        $collection->push(['VENTES PAR PRODUIT']);
        $collection->push(['Produit', 'Catégorie', 'Quantité vendue', 'CA', 'Bénéfice']);
        
        foreach ($this->data['ventesParProduit'] as $produitData) {
            $collection->push([
                $produitData['produit']->nom,
                $produitData['produit']->categorie,
                $produitData['quantite'],
                number_format($produitData['ca'], 0, ',', ' ') . ' FCFA',
                number_format($produitData['benefice'], 0, ',', ' ') . ' FCFA'
            ]);
        }
        $collection->push([]);

        // Ajouter le détail des ventes
        $collection->push(['DÉTAIL DES VENTES']);
        $collection->push(['Date', 'Produit', 'Boutique', 'Magasin', 'Quantité', 'Prix unitaire', 'Total', 'Bénéfice']);
        
        foreach ($this->data['venteProduits'] as $vp) {
            $collection->push([
                $vp->vente->date_vente->format('d/m/Y'),
                $vp->produit->nom,
                $vp->vente->boutique->nom,
                $vp->vente->boutique->magasin->nom,
                $vp->quantite,
                number_format($vp->prix_unitaire, 0, ',', ' ') . ' FCFA',
                number_format($vp->sous_total, 0, ',', ' ') . ' FCFA',
                number_format(($vp->prix_unitaire - $vp->produit->prix_achat) * $vp->quantite, 0, ',', ' ') . ' FCFA'
            ]);
        }

        return $collection;
    }

    public function headings(): array
    {
        // Les en-têtes sont inclus dans la collection pour plus de flexibilité
        return [];
    }

    public function title(): string
    {
        return 'Rapport Ventes';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['italic' => true]],
            3 => ['font' => ['italic' => true]],
            4 => ['font' => ['italic' => true]],
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['bold' => true]],
            11 => ['font' => ['bold' => true]],
            12 => ['font' => ['bold' => true]],
            // Styles pour les en-têtes de tableaux
            'A' => ['font' => ['bold' => true]],
            'B' => ['font' => ['bold' => true]],
            'C' => ['font' => ['bold' => true]],
            'D' => ['font' => ['bold' => true]],
            'E' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // Date
            'B' => 25,  // Produit/Boutique
            'C' => 20,  // Catégorie/Magasin
            'D' => 12,  // Quantité/Nombre ventes
            'E' => 18,  // Prix/CA
            'F' => 18,  // Total/Bénéfice
        ];
    }
}
