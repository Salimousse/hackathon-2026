<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commentaire;
use App\Models\MembreAsso;
use Illuminate\Support\Facades\Auth;

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

        // Récupérer les commentaires
        $commentaires = Commentaire::where('idAssociation', $id)
            ->with('user')
            ->orderBy('idCommentaire', 'desc')
            ->get();

        // Calculer la moyenne des notes
        $moyenneNote = Commentaire::where('idAssociation', $id)->avg('noteAssociation');
        
        // Vérifier si l'utilisateur a déjà commenté
        $aDejaCommente = false;
        if (Auth::check()) {
            $aDejaCommente = Commentaire::where('idUser', Auth::id())
                ->where('idAssociation', $id)
                ->exists();
        }
        
        // Vérifier si l'utilisateur est membre de cette association
        $estMembre = false;
        $adhesion = null;
        if (Auth::check()) {
            $adhesion = MembreAsso::where('user_id', Auth::id())
                ->where('association_id', $id)
                ->first();
            $estMembre = $adhesion && $adhesion->status === 'accepted';
        }

        return view('info-associations', [
            'association' => $association,
            'commentaires' => $commentaires,
            'moyenneNote' => $moyenneNote,
            'aDejaCommente' => $aDejaCommente,
            'estMembre' => $estMembre,
            'adhesion' => $adhesion
        ]);
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

    // Ajouter un commentaire
    public function ajouterCommentaire(Request $request)
    {
        $request->validate([
            'idAssociation' => 'required|string',
            'noteAssociation' => 'required|integer|min:1|max:5',
            'descCommentaire' => 'required|string|max:500'
        ]);

        // Vérifier si l'utilisateur a déjà commenté
        $existe = Commentaire::where('idUser', Auth::id())
            ->where('idAssociation', $request->idAssociation)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà laissé un avis pour cette association.');
        }

        Commentaire::create([
            'idUser' => Auth::id(),
            'idAssociation' => $request->idAssociation,
            'noteAssociation' => $request->noteAssociation,
            'descCommentaire' => $request->descCommentaire
        ]);

        return back()->with('success', 'Votre avis a été ajouté avec succès!');
    }

    // Supprimer un commentaire
    public function supprimerCommentaire($id)
    {
        $commentaire = Commentaire::findOrFail($id);

        if ($commentaire->idUser !== Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer cet avis.');
        }

        $commentaire->delete();

        return back()->with('success', 'Votre avis a été supprimé.');
    }
    
    // Rejoindre une association
    public function rejoindre($id)
    {
        // Vérifier si l'utilisateur est déjà membre
        $existe = MembreAsso::where('user_id', Auth::id())
            ->where('association_id', $id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà demandé à rejoindre cette association.');
        }

        MembreAsso::create([
            'user_id' => Auth::id(),
            'association_id' => $id,
            'role' => 'membre',
            'status' => 'accepted' // Acceptation automatique pour le moment
        ]);

        return back()->with('success', 'Vous avez rejoint l\'association avec succès!');
    }
    
    // Quitter une association
    public function quitter($id)
    {
        $adhesion = MembreAsso::where('user_id', Auth::id())
            ->where('association_id', $id)
            ->first();

        if (!$adhesion) {
            return back()->with('error', 'Vous n\'êtes pas membre de cette association.');
        }

        $adhesion->delete();

        return back()->with('success', 'Vous avez quitté l\'association.');
    }
}


