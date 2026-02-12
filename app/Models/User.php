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
        'boutique_id',
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
    public function magasinResponsable()
    {
        return $this->belongsTo(Magasin::class, 'magasin_id');
    }

    public function magasinsGeres()
    {
        return $this->hasMany(Magasin::class, 'responsable_id');
    }

    public function boutique()
    {
        return $this->belongsTo(Boutique::class, 'boutique_id');
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
