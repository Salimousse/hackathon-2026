<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembreAsso extends Model
{
    protected $table = 'membre_asso';
    
    protected $fillable = [
        'user_id',
        'association_id',
        'role',
        'status'
    ];
    
    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Vérifier si l'adhésion est acceptée
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }
    
    /**
     * Vérifier si l'adhésion est en attente
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
