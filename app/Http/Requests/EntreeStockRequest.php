<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EntreeStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produit_id' => 'required|exists:produits,id',
            'magasin_id' => 'required|exists:magasins,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'partenaire_id' => 'nullable|exists:partenaires,id',
            'order_id' => 'nullable|exists:orders,id',
            'quantite' => 'required|integer|min:1|max:2147483647',
            'prix_unitaire' => 'required|numeric|min:0',
            'date' => 'required|date',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (!$this->fournisseur_id && !$this->partenaire_id) {
                $validator->errors()->add('fournisseur_id', 'Vous devez sélectionner au moins un fournisseur ou un partenaire');
            }

            if ($this->fournisseur_id && $this->partenaire_id) {
                $validator->errors()->add('fournisseur_id', 'Sélectionnez soit un fournisseur, soit un partenaire (pas les deux)');
            }
        });
    }
}
