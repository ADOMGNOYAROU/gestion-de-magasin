<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransfertRequest extends FormRequest
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
            'boutique_id' => 'required|exists:boutiques,id',
            'quantite' => 'required|integer|min:1',
            'date' => 'required|date',
        ];
    }
}
