<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssociationController extends Controller
{

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

        if ($search) {
            $params['where'] = "title like '%{$search}%'";
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

    // fonction pour lister les associations avec pagination
    public function list(Request $request)
    {
        $endpoint = "https://hub.huwise.com/api/explore/v2.1/catalog/datasets/ref-france-association-repertoire-national/records";
        
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        $params = [
            'limit' => $perPage,
            'offset' => $offset
        ];

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

        return view('recherche_associations', ['results' => $paginator, 'query' => null]);
    }
}