<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartenaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $partenaireId = $this->route('partenaire')?->id;

        return [
            'nom' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'telephone' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:255', 'unique:partenaires,email,' . $partenaireId],
            'type_partenariat' => 'required|string|max:255',
        ];
    }
}
