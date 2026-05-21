<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = config('services.paystack.base_url', 'https://api.paystack.co');
    }

    /**
     * Créer un client Paystack
     */
    public function createCustomer(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/customer', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack createCustomer error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Initialiser une transaction de paiement
     */
    public function initializeTransaction(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack initializeTransaction error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifier une transaction
     */
    public function verifyTransaction($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack verifyTransaction error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Créer un plan d'abonnement
     */
    public function createPlan(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/plan', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack createPlan error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Créer un abonnement
     */
    public function createSubscription(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/subscription', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack createSubscription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Annuler un abonnement
     */
    public function cancelSubscription($subscriptionCode, $emailToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->post($this->baseUrl . '/subscription/' . $subscriptionCode . '/disable', [
                'email_token' => $emailToken,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack cancelSubscription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Activer un abonnement
     */
    public function enableSubscription($subscriptionCode, $emailToken)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->post($this->baseUrl . '/subscription/' . $subscriptionCode . '/enable', [
                'email_token' => $emailToken,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack enableSubscription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lister les abonnements d'un client
     */
    public function listSubscriptions($customerEmail)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/subscription', [
                'customer' => $customerEmail,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Paystack listSubscriptions error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtenir la clé publique
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}
