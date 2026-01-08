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
    
    /**
     * Relation avec les associations rejointes
     */
    public function associations()
    {
        return $this->hasMany(MembreAsso::class);
    }
    
    /**
     * Vérifier si l'utilisateur est membre d'une association
     */
    public function isMemberOf($associationId)
    {
        return $this->associations()
            ->where('association_id', $associationId)
            ->where('status', 'accepted')
            ->exists();
    }
    
    /**
     * Obtenir les associations acceptées uniquement
     */
    public function associationsAcceptees()
    {
        return $this->associations()->where('status', 'accepted');
    }
}
