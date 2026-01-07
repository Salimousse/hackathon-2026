<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssociationController extends Controller
{



    // fonction de recherche des associations
    public function search (Request $request)
    {
        $search = $request->input('query');
        $endpoint = "https://hub.huwise.com/api/explore/v2.1/catalog/datasets/ref-france-association-repertoire-national/records";
        
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $params = [
            'limit' => $perPage,
            'offset' => $offset
        ];

        // Construction de la clause WHERE avec plusieurs critères
        $whereClauses = [];

        // 1. Recherche par mot-clé (Titre ou Objet)
        if ($search) {
            // On cherche dans le titre ou la description
            $whereClauses[] = "(title like '%{$search}%' OR object like '%{$search}%')";
        }

        // 2. Filtre par Ville (si présent dans la requête)
        if ($ville = $request->input('ville')) {
            $whereClauses[] = "com_name_asso like '%{$ville}%'";
        }

        // 3. Filtre par Code Postal (si présent dans la requête)
        if ($cp = $request->input('cp')) {
            $whereClauses[] = "pc_address_asso like '{$cp}%'";
        }

        // 4. Filtre par géolocalisation 
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        
        if ($lat && $lon) {
    // Sécurité : On s'assure que ce sont des points et non des virgules
    // et on force le type float pour la sécurité 
    $lat = number_format((float)$lat, 6, '.', '');
    $lon = number_format((float)$lon, 6, '.', '');
    $whereClauses[] = "distance(geo_point_2d, geom'POINT($lon $lat)', 20km)";
}



        

        // On assemble tous les filtres avec AND
        if (count($whereClauses) > 0) {
            $params['where'] = implode(' AND ', $whereClauses);
        }

        $url = $endpoint . '?' . http_build_query($params);

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        $results = [];
        $total = 0;

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $results = $data['results'] ?? [];
            $total = $data['total_count'] ?? 0;
        }

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('recherche_associations', ['results' => $paginator, 'query' => $search]);
    }


    // afficher les infos d'une association
    public function show($id)
    {
        $endpoint = "https://hub.huwise.com/api/explore/v2.1/catalog/datasets/ref-france-association-repertoire-national/records";
        
        $params = [
            'where' => "id = '{$id}'",
            'limit' => 1
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->get($endpoint . '?' . http_build_query($params));

        $association = null;

        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);
            $association = $data['results'][0] ?? null;
        }

        return view('info-associations', ['association' => $association]);
    }


    public function list(Request $request)
    {
        //fonction pour pagination 
        return $this->search($request);
    }


    public function reinitialiserFiltres()
    {
        // Rediriger vers la page de recherche sans paramètres
        return redirect()->route('recherche.associations');
    }

    


}


