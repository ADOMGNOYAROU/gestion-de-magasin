<?php

namespace App\Imports;

use App\Models\Produit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProduitsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Produit([
            'nom' => $row['nom'],
            'categorie' => $row['categorie'],
            'prix_vente' => $row['prix_vente'],
            'prix_achat' => $row['prix_achat'] ?? null,
            'reference' => $row['reference'] ?? null,
            'seuil_alerte' => $row['seuil_alerte'] ?? 5,
            'statut' => 'actif',
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'prix_vente' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|numeric|min:0',
            'reference' => 'nullable|string|max:255',
            'seuil_alerte' => 'nullable|integer|min:0',
        ];
    }
}
