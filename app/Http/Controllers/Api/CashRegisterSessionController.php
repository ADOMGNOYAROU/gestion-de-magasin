<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashRegisterSession;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CashRegisterSessionController extends Controller
{
    public function current(Request $request)
    {
        $session = CashRegisterSession::where('vendeur_id', $request->user()->id)
            ->where('status', 'ouverte')
            ->latest('date_ouverture')
            ->first();

        return response()->json($session ? $this->format($session) : null);
    }

    public function open(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'boutique_id' => ['nullable', 'exists:boutiques,id'],
            'montant_initial' => ['required', 'numeric', 'min:0'],
        ]);

        $boutiqueId = $data['boutique_id'] ?? $user->boutique_id;

        if (! $boutiqueId) {
            throw ValidationException::withMessages([
                'boutique_id' => ['Aucune boutique associée à cet utilisateur.'],
            ]);
        }

        $existing = CashRegisterSession::where('vendeur_id', $user->id)
            ->where('status', 'ouverte')
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'session' => ['Une session de caisse est déjà ouverte.'],
            ]);
        }

        $session = CashRegisterSession::create([
            'vendeur_id' => $user->id,
            'boutique_id' => $boutiqueId,
            'montant_initial' => $data['montant_initial'],
            'montant_theorique' => $data['montant_initial'],
            'date_ouverture' => now(),
            'status' => 'ouverte',
        ]);

        return response()->json($this->format($session), 201);
    }

    public function close(Request $request, CashRegisterSession $cashRegisterSession)
    {
        $data = $request->validate([
            'montant_final' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($cashRegisterSession->vendeur_id !== $request->user()->id) {
            abort(403);
        }

        $cashRegisterSession->fermer($data['montant_final'], $data['notes'] ?? null);

        return response()->json($this->format($cashRegisterSession));
    }

    private function format(CashRegisterSession $s): array
    {
        return [
            'id' => $s->id,
            'boutique_id' => $s->boutique_id,
            'montant_initial' => (float) $s->montant_initial,
            'montant_final' => $s->montant_final !== null ? (float) $s->montant_final : null,
            'montant_theorique' => (float) $s->montant_theorique,
            'ecart' => (float) $s->ecart,
            'status' => $s->status,
            'date_ouverture' => $s->date_ouverture?->toIso8601String(),
            'date_fermeture' => $s->date_fermeture?->toIso8601String(),
        ];
    }
}
