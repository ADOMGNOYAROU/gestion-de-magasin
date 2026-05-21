<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use HasFactory, SoftDeletes, Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'postal_code',
        'plan',
        'payment_provider', // stripe, paystack, flutterwave
        'stripe_id',
        'paystack_customer_code',
        'flutterwave_customer_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'is_active',
        'subscription_ends_at',
        'paystack_subscription_code',
        'flutterwave_subscription_id',
        'paystack_email_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'subscription_ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the users for the tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the magasins for the tenant.
     */
    public function magasins()
    {
        return $this->hasMany(Magasin::class);
    }

    /**
     * Get the boutiques for the tenant.
     */
    public function boutiques()
    {
        return $this->hasManyThrough(Boutique::class, Magasin::class);
    }

    /**
     * Get the produits for the tenant.
     */
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }

    /**
     * Get the ventes for the tenant.
     */
    public function ventes()
    {
        return $this->hasManyThrough(Vente::class, Boutique::class);
    }

    /**
     * Get the plan configuration.
     */
    public function getPlanConfig()
    {
        return config('plans.' . $this->plan, []);
    }

    /**
     * Check if tenant is on trial.
     */
    public function isOnTrial()
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant can add more users.
     */
    public function canAddUser()
    {
        $plan = $this->getPlanConfig();
        $maxUsers = $plan['max_users'] ?? -1;

        if ($maxUsers === -1) {
            return true;
        }

        return $this->users()->count() < $maxUsers;
    }

    /**
     * Check if tenant can add more magasins.
     */
    public function canAddMagasin()
    {
        $plan = $this->getPlanConfig();
        $maxMagasins = $plan['max_magasins'] ?? -1;

        if ($maxMagasins === -1) {
            return true;
        }

        return $this->magasins()->count() < $maxMagasins;
    }

    /**
     * Check if tenant can add more boutiques.
     */
    public function canAddBoutique()
    {
        $plan = $this->getPlanConfig();
        $maxBoutiques = $plan['max_boutiques'] ?? -1;

        if ($maxBoutiques === -1) {
            return true;
        }

        return $this->boutiques()->count() < $maxBoutiques;
    }

    /**
     * Check if tenant has access to a specific feature.
     */
    public function hasFeature($feature)
    {
        $plan = $this->getPlanConfig();
        $features = $plan['features'] ?? [];

        if (in_array('all', $features)) {
            return true;
        }

        return in_array($feature, $features);
    }

    /**
     * Get subscription status label.
     */
    public function getSubscriptionStatusLabelAttribute()
    {
        return match($this->subscription_status) {
            'trial' => 'Essai gratuit',
            'active' => 'Actif',
            'cancelled' => 'Annulé',
            'expired' => 'Expiré',
            default => 'Inconnu',
        };
    }

    /**
     * Get subscription status color.
     */
    public function getSubscriptionStatusColorAttribute()
    {
        return match($this->subscription_status) {
            'trial' => 'info',
            'active' => 'success',
            'cancelled' => 'warning',
            'expired' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope to filter by active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by plan.
     */
    public function scopeByPlan($query, $plan)
    {
        return $query->where('plan', $plan);
    }

    /**
     * Get the payment provider service.
     */
    public function getPaymentProviderService()
    {
        $provider = $this->payment_provider ?? 'stripe';

        return match($provider) {
            'paystack' => new \App\Services\PaystackService(),
            'flutterwave' => new \App\Services\FlutterwaveService(),
            default => null, // Stripe handled by Cashier
        };
    }

    /**
     * Check if tenant has active subscription (works with all providers).
     */
    public function hasActiveSubscription()
    {
        if ($this->isOnTrial()) {
            return true;
        }

        if (!$this->subscription_ends_at) {
            return false;
        }

        return $this->subscription_ends_at->isFuture();
    }

    /**
     * Get subscription status text (works with all providers).
     */
    public function getSubscriptionStatusAttribute()
    {
        if ($this->isOnTrial()) {
            return 'trial';
        }

        if (!$this->hasActiveSubscription()) {
            return 'expired';
        }

        // Check for Stripe subscription
        if ($this->payment_provider === 'stripe' || !$this->payment_provider) {
            if ($this->subscription('default') && $this->subscription('default')->cancelled()) {
                return 'cancelled';
            }
        }

        // Check for Paystack subscription
        if ($this->payment_provider === 'paystack' && $this->paystack_subscription_code) {
            // Would need to check Paystack API for status
            return 'active';
        }

        // Check for Flutterwave subscription
        if ($this->payment_provider === 'flutterwave' && $this->flutterwave_subscription_id) {
            // Would need to check Flutterwave API for status
            return 'active';
        }

        return 'active';
    }
}
