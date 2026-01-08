<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle MembreAsso - Représente l'adhésion d'un utilisateur à une association.
 * 
 * @property int $id Identifiant unique de l'adhésion
 * @property int $user_id Identifiant de l'utilisateur
 * @property string $association_id Identifiant de l'association
 * @property string $role Rôle dans l'association (membre, admin, etc.)
 * @property string $status Statut de l'adhésion (accepted, pending)
 */
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
     * Relation : Utilisateur associé à cette adhésion.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Vérifie si l'adhésion est acceptée.
     *
     * @return bool True si statut = 'accepted', false sinon
     */
    public function isAccepted()
    {
        return $this->status === 'accepted';
    }
    
    /**
     * Vérifie si l'adhésion est en attente.
     *
     * @return bool True si statut = 'pending', false sinon
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }
}
