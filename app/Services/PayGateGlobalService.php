<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Client pour l'API PayGateGlobal (paiements mobile money Flooz / T-Money).
 *
 * Documentation : https://paygateglobal.com/api/v1
 */
class PayGateGlobalService
{
    private string $baseUrl;
    private string $authToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.paygate.base_url'), '/');
        $this->authToken = (string) config('services.paygate.auth_token');
    }

    /**
     * Initie une demande de paiement mobile money (Méthode 1 : appel serveur direct).
     *
     * @param string $phoneNumber Numéro de téléphone du client
     * @param float $amount Montant en FCFA
     * @param string $identifier Identifiant unique côté e-commerce
     * @param string $network 'FLOOZ' ou 'TMONEY'
     * @param string|null $description
     */
    public function pay(string $phoneNumber, float $amount, string $identifier, string $network, ?string $description = null): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}/api/v1/pay", [
            'auth_token' => $this->authToken,
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'identifier' => $identifier,
            'network' => $network,
            'description' => $description,
        ]);

        $this->logIfFailed('pay', $response);

        return $response->json() ?? [];
    }

    /**
     * Vérifie le statut d'une transaction via la référence PayGateGlobal.
     */
    public function status(string $txReference): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}/api/v1/status", [
            'auth_token' => $this->authToken,
            'tx_reference' => $txReference,
        ]);

        $this->logIfFailed('status', $response);

        return $response->json() ?? [];
    }

    /**
     * Vérifie le statut d'une transaction via l'identifiant unique côté e-commerce.
     */
    public function statusByIdentifier(string $identifier): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}/api/v2/status", [
            'auth_token' => $this->authToken,
            'identifier' => $identifier,
        ]);

        $this->logIfFailed('statusByIdentifier', $response);

        return $response->json() ?? [];
    }

    /**
     * Consulte le solde Flooz / TMoney du compte marchand.
     * Nécessite que l'IP du serveur soit whitelistée par PayGateGlobal.
     */
    public function checkBalance(): array
    {
        $response = Http::asForm()->post("{$this->baseUrl}/api/v1/check-balance", [
            'auth_token' => $this->authToken,
        ]);

        $this->logIfFailed('checkBalance', $response);

        return $response->json() ?? [];
    }

    private function logIfFailed(string $operation, $response): void
    {
        if ($response->failed()) {
            Log::warning("PayGateGlobal [{$operation}] a échoué", [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }
}
