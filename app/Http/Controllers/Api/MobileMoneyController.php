<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileMoneyPayment;
use App\Notifications\PaiementMobileMoneyConfirme;
use App\Services\PayGateGlobalService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MobileMoneyController extends Controller
{
    public function __construct(private PayGateGlobalService $payGate)
    {
    }

    /**
     * Initie une demande de paiement mobile money (Flooz / T-Money).
     * Le paiement reste "pending" tant que le client n'a pas validé sur son téléphone ;
     * l'app mobile doit ensuite sonder GET /mobile-money/{identifier}/status.
     */
    public function pay(Request $request)
    {
        $data = $request->validate([
            'phone_number' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:1'],
            'network' => ['required', 'in:FLOOZ,TMONEY'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $identifier = 'MM-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $payment = MobileMoneyPayment::create([
            'user_id' => $request->user()->id,
            'identifier' => $identifier,
            'phone_number' => $data['phone_number'],
            'network' => $data['network'],
            'amount' => $data['amount'],
            'status' => 'pending',
        ]);

        $response = $this->payGate->pay(
            $data['phone_number'],
            $data['amount'],
            $identifier,
            $data['network'],
            $data['description'] ?? 'Paiement Majoie Gestion',
        );

        $code = $response['status'] ?? null;

        if ($code !== 0 && $code !== '0') {
            $payment->update([
                'status' => 'failed',
                'raw_response' => $response,
            ]);

            return response()->json([
                'identifier' => $identifier,
                'status' => 'failed',
                'message' => $this->payErrorMessage($code),
            ], 422);
        }

        $payment->update([
            'tx_reference' => $response['tx_reference'] ?? null,
            'raw_response' => $response,
        ]);

        return response()->json([
            'identifier' => $identifier,
            'status' => 'pending',
            'message' => 'Demande envoyée. Le client doit valider sur son téléphone.',
        ], 201);
    }

    /**
     * Sonde le statut d'une demande de paiement mobile money.
     */
    public function status(Request $request, string $identifier)
    {
        $payment = MobileMoneyPayment::where('identifier', $identifier)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if (! in_array($payment->status, ['success', 'failed', 'expired', 'cancelled'], true)) {
            $response = $this->payGate->statusByIdentifier($identifier);
            $code = $response['status'] ?? null;

            $status = match ((string) $code) {
                '0' => 'success',
                '2' => 'pending',
                '4' => 'expired',
                '6' => 'cancelled',
                default => $payment->status,
            };

            $payment->update([
                'status' => $status,
                'tx_reference' => $response['tx_reference'] ?? $payment->tx_reference,
                'payment_reference' => $response['payment_reference'] ?? $payment->payment_reference,
                'paid_at' => $status === 'success' ? now() : $payment->paid_at,
                'raw_response' => $response,
            ]);

            if ($status === 'success') {
                $payment->user->notify(new PaiementMobileMoneyConfirme($payment));
            }
        }

        return response()->json([
            'identifier' => $payment->identifier,
            'status' => $payment->status,
            'payment_reference' => $payment->payment_reference,
        ]);
    }

    /**
     * Consulte le solde marchand Flooz / TMoney (admin uniquement).
     * Nécessite que l'IP du serveur soit whitelistée par PayGateGlobal.
     */
    public function balance(Request $request)
    {
        abort_unless($request->user()->role === 'admin', 403);

        return response()->json($this->payGate->checkBalance());
    }

    /**
     * Webhook public appelé par PayGateGlobal à la confirmation du paiement.
     * Nécessite une URL publiquement accessible (non joignable depuis localhost).
     */
    public function webhook(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'tx_reference' => ['nullable', 'string'],
            'payment_reference' => ['nullable', 'string'],
        ]);

        $payment = MobileMoneyPayment::where('identifier', $data['identifier'])->first();

        if ($payment && $payment->status !== 'success') {
            $payment->update([
                'status' => 'success',
                'tx_reference' => $data['tx_reference'] ?? $payment->tx_reference,
                'payment_reference' => $data['payment_reference'] ?? $payment->payment_reference,
                'paid_at' => now(),
                'raw_response' => $request->all(),
            ]);

            $payment->user->notify(new PaiementMobileMoneyConfirme($payment));
        }

        return response()->json(['message' => 'ok']);
    }

    private function payErrorMessage($code): string
    {
        return match ((string) $code) {
            '2' => 'Jeton d\'authentification invalide.',
            '4' => 'Paramètres invalides.',
            '6' => 'Une transaction identique existe déjà.',
            default => 'Échec de la demande de paiement.',
        };
    }
}
