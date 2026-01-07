<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class AssociationSearch extends Component
{
    use WithPagination; // Permet la pagination sans rechargement

    // Variables connectées au formulaire
    public $query = '';
    public $ville = '';
    public $cp = '';
    public $lat = '';
    public $lon = '';

    // Remet la page à 1 dès qu'on tape une recherche
    public function updatedQuery() { $this->resetPage(); }
    public function updatedVille() { $this->resetPage(); }
    public function updatedLat() { $this->resetPage(); }
    public function updatedLon() { $this->resetPage(); }

    public function render()
    {
        // 1. Préparation de la requête API
        $endpoint = "https://hub.huwise.com/api/explore/v2.1/catalog/datasets/ref-france-association-repertoire-national/records";
        $perPage = 10;
        $page = $this->getPage(); // Livewire gère la page actuelle
        
        $params = [
            'limit' => $perPage,
            'offset' => ($page - 1) * $perPage,
        ];

        $whereClauses = [];

        // Recherche par mot clé
        if (!empty($this->query)) {
            $whereClauses[] = "(title like '%" . addslashes($this->query) . "%' OR object like '%" . addslashes($this->query) . "%')";
        }

        // Filtre Ville / CP
        if (!empty($this->ville)) {
            $whereClauses[] = "com_name_asso like '%" . addslashes($this->ville) . "%'";
        }
        if (!empty($this->cp)) {
            $whereClauses[] = "pc_address_asso like '" . addslashes($this->cp) . "%'";
        }

        // Filtre Géolocalisation (Correction du problème virgule/point)
        if (!empty($this->lat) && !empty($this->lon)) {
            $lat = number_format((float)$this->lat, 6, '.', '');
            $lon = number_format((float)$this->lon, 6, '.', '');
            // Syntaxe API pour chercher à 20km autour
            $whereClauses[] = "distance(geo_point_2d, geom'POINT($lon $lat)', 20km)";
        }

        if (count($whereClauses) > 0) {
            $params['where'] = implode(' AND ', $whereClauses);
        }

        // 2. Appel API
        $results = [];
        $total = 0;

        try {
            $client = new Client();
            $response = $client->get($endpoint, ['query' => $params]);
            $data = json_decode($response->getBody(), true);
            
            $results = $data['results'] ?? [];
            $total = $data['total_count'] ?? 0;
        } catch (\Exception $e) {
            // En cas d'erreur, on laisse la liste vide
        }

        // 3. Création de la pagination manuelle
        $paginator = new LengthAwarePaginator(
            $results, 
            $total, 
            $perPage, 
            $page, 
            ['path' => request()->url()]
        );

        return view('livewire.association-search', [
            'results' => $paginator
        ]);
    }
}