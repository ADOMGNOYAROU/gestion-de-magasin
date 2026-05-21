<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FlutterwaveService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.flutterwave.secret_key');
        $this->publicKey = config('services.flutterwave.public_key');
        $this->baseUrl = config('services.flutterwave.base_url', 'https://api.flutterwave.com/v3');
    }

    /**
     * Créer un client Flutterwave
     */
    public function createCustomer(array $data)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/customers', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave createCustomer error: ' . $e->getMessage());
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
            ])->post($this->baseUrl . '/payments', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave initializeTransaction error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifier une transaction
     */
    public function verifyTransaction($transactionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transactions/' . $transactionId . '/verify');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave verifyTransaction error: ' . $e->getMessage());
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
            ])->post($this->baseUrl . '/payment-plans', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave createPlan error: ' . $e->getMessage());
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
            ])->post($this->baseUrl . '/subscriptions', $data);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave createSubscription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Annuler un abonnement
     */
    public function cancelSubscription($subscriptionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->put($this->baseUrl . '/subscriptions/' . $subscriptionId . '/cancel');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave cancelSubscription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Activer un abonnement
     */
    public function activateSubscription($subscriptionId)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->put($this->baseUrl . '/subscriptions/' . $subscriptionId . '/activate');

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave activateSubscription error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lister les abonnements d'un client
     */
    public function listSubscriptions($email)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/subscriptions', [
                'email' => $email,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Flutterwave listSubscriptions error: ' . $e->getMessage());
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
