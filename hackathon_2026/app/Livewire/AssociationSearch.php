<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use GuzzleHttp\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class AssociationSearch extends Component
{
    use WithPagination;

    /**
     * Mot-clé de recherche (titre ou objet de l'association).
     * 
     * @var string
     */
    public $query = '';

    /**
     * Nom de la ville pour filtrer les associations.
     * 
     * @var string
     */
    public $ville = '';

    /**
     * Code postal pour filtrer les associations.
     * 
     * @var string
     */
    public $cp = '';

    /**
     * Latitude pour la recherche géolocalisée.
     * 
     * @var string
     */
    public $lat = '';

    /**
     * Longitude pour la recherche géolocalisée.
     * 
     * @var string
     */
    public $lon = '';

    /**
     * Initialise le composant avec les paramètres de l'URL.
     *
     * @return void
     */
    public function mount()
    {
        $this->query = request('query', '');
        $this->ville = request('ville', '');
        $this->cp = request('cp', '');
        $this->lat = request('lat', '');
        $this->lon = request('lon', '');
    }

    /**
     * Réinitialise la pagination quand la recherche change.
     *
     * @return void
     */
    public function updatedQuery() { $this->resetPage(); }

    /**
     * Réinitialise la pagination quand la ville change.
     *
     * @return void
     */
    public function updatedVille() { $this->resetPage(); }

    /**
     * Réinitialise la pagination quand la latitude change.
     *
     * @return void
     */
    public function updatedLat() { $this->resetPage(); }

    /**
     * Réinitialise la pagination quand la longitude change.
     *
     * @return void
     */
    public function updatedLon() { $this->resetPage(); }

    /**
     * Réinitialise tous les filtres de recherche.
     * 
     * Efface tous les champs de recherche et réinitialise la pagination.
     *
     * @return void
     */
    public function resetFilters()
    {
        $this->query = '';
        $this->ville = '';
        $this->cp = '';
        $this->lat = '';
        $this->lon = '';
        $this->resetPage();
    }

    /**
     * Affiche les résultats de recherche d'associations.
     * 
     * Effectue une requête vers l'API Huwise avec les filtres appliqués :
     * - Recherche par mot-clé (titre/objet)
     * - Filtre par ville et code postal
     * - Recherche géolocalisée dans un rayon de 20km
     * 
     * Retourne une vue avec les résultats paginés.
     *
     * @return \Illuminate\View\View Vue Livewire avec les associations trouvées
     */
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