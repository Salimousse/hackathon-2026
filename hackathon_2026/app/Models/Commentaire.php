<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class, 'idUser');
    }

    /**
     * Récupérer le nom de l'association via l'API
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
