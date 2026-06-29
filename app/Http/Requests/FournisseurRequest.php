<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FournisseurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fournisseurId = $this->route('fournisseur')?->id;

        return [
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', 'unique:fournisseurs,email,' . $fournisseurId],
            'contact_personne' => 'required|string|max:255',
        ];
    }
}
