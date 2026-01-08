<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Commentaire - Représente un avis/commentaire sur une association.
 * 
 * @property int $idCommentaire Identifiant unique du commentaire
 * @property int $idUser Identifiant de l'utilisateur ayant posté le commentaire
 * @property string $idAssociation Identifiant de l'association commentée
 * @property int $noteAssociation Note de 1 à 5 étoiles
 * @property string $descCommentaire Description textuelle du commentaire
 */
class Commentaire extends Model
{
    protected $table = 'commentaire';
    protected $primaryKey = 'idCommentaire'; // Spécifier la clé primaire
    
    public $timestamps = false; // Désactiver les timestamps automatiques

    protected $fillable = [
        'idUser',
        'descCommentaire',
        'idAssociation',
        'noteAssociation',
        
    ];

    /**
     * Relation : Utilisateur ayant posté ce commentaire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    /**
     * Accesseur : Récupère le nom de l'association via l'API externe.
     * 
     * Effectue un appel à l'API Huwise pour obtenir le titre de l'association.
     * Retourne 'Association inconnue' en cas d'erreur.
     *
     * @return string Nom de l'association ou 'Association inconnue'
     */
    public function getNomAssociationAttribute()
    {
        try {
            $client = new \GuzzleHttp\Client();
            $endpoint = "https://hub.huwise.com/api/explore/v2.1/catalog/datasets/ref-france-association-repertoire-national/records";
            
            $params = [
                'where' => "id = '{$this->idAssociation}'",
                'limit' => 1
            ];

            $response = $client->get($endpoint . '?' . http_build_query($params));
            
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(), true);
                return $data['results'][0]['title'] ?? 'Association inconnue';
            }
        } catch (\Exception $e) {
            return 'Association inconnue';
        }
        
        return 'Association inconnue';
    }
}
