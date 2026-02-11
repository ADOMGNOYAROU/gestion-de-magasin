<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'magasin_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function magasin()
    {
        return $this->belongsTo(Magasin::class);
    }

    public function magasinsResponsables()
    {
        return $this->hasMany(Magasin::class, 'responsable_id');
    }

    public function magasinResponsable()
    {
        return $this->hasOne(Magasin::class, 'responsable_id');
    }

    public function boutique()
    {
        return $this->hasOne(Boutique::class, 'vendeur_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isGestionnaire()
    {
        return $this->role === 'gestionnaire';
    }

    public function isVendeur()
    {
        return $this->role === 'vendeur';
    }
}
