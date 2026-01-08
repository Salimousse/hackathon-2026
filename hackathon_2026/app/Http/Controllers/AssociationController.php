<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commentaire;
use App\Models\MembreAsso;
use Illuminate\Support\Facades\Auth;

class AssociationController extends Controller
{
    /**
     * Recherche des associations via l'API Huwise.
     * 
     * Permet de rechercher par mot-clé (titre/objet), ville, code postal
     * ou par géolocalisation (rayon de 20km).
     *
     * @param Request $request Paramètres : query, ville, cp, lat, lon, page
     * @return \Illuminate\View\View Vue avec les résultats paginés
     */
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
            
            // Ajouter la distance comme champ sélectionné
            $params['select'] = "*, distance(geo_point_2d, geom'POINT($lon $lat)', 20km) as distance_km";
            
            // Filtrer dans un rayon de 20km
            $whereClauses[] = "distance(geo_point_2d, geom'POINT($lon $lat)', 20km)";
            
            // Trier par distance (du plus proche au plus loin)
            $params['order_by'] = "distance(geo_point_2d, geom'POINT($lon $lat)') ASC";
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

    /**
     * Affiche les détails complets d'une association.
     * 
     * Récupère les données de l'association depuis l'API,
     * les commentaires/avis et vérifie le statut de l'utilisateur
     * (membre, déjà commenté).
     *
     * @param string $id Identifiant unique de l'association
     * @return \Illuminate\View\View Vue avec les détails de l'association
     */
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

    /**
     * Liste les associations avec pagination.
     * 
     * Alias de la méthode search pour la pagination.
     *
     * @param Request $request Paramètres de recherche et pagination
     * @return \Illuminate\View\View Vue avec les résultats paginés
     */
    public function list(Request $request)
    {
        //fonction pour pagination 
        return $this->search($request);
    }

    /**
     * Réinitialise tous les filtres de recherche.
     *
     * @return \Illuminate\Http\RedirectResponse Redirection vers la page de recherche vierge
     */
    public function reinitialiserFiltres()
    {
        // Rediriger vers la page de recherche sans paramètres
        return redirect()->route('recherche.associations');
    }

    /**
     * Ajoute un commentaire/avis pour une association.
     * 
     * Vérifie que l'utilisateur n'a pas déjà commenté cette association
     * (limitation : 1 avis par utilisateur et par association).
     *
     * @param Request $request Données : idAssociation, noteAssociation (1-5), descCommentaire
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès ou erreur
     */
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

    /**
     * Supprime un commentaire de l'utilisateur.
     * 
     * Seul l'auteur du commentaire peut le supprimer.
     *
     * @param int $id ID du commentaire à supprimer
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès ou erreur
     */
    public function supprimerCommentaire($id)
    {
        $commentaire = Commentaire::findOrFail($id);

        if ($commentaire->idUser !== Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer cet avis.');
        }

        $commentaire->delete();

        return back()->with('success', 'Votre avis a été supprimé.');
    }
    
    /**
     * Permet à l'utilisateur de rejoindre une association.
     * 
     * Crée une adhésion avec le statut 'accepted' directement.
     *
     * @param string $id ID de l'association à rejoindre
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès ou erreur
     */
    public function rejoindre($id)
    {
        // Vérifier si l'utilisateur est déjà membre
        $existe = MembreAsso::where('user_id', Auth::id())
            ->where('association_id', $id)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà demandé à rejoindre cette association.');
        }


        // rajoute l'utilisateur dans la table membre asso 
        MembreAsso::create([
            'user_id' => Auth::id(),
            'association_id' => $id,
            'role' => 'membre',
            'status' => 'accepted' 
        ]);

        return back()->with('success', 'Vous avez rejoint l\'association avec succès!');
    }
    
    /**
     * Permet à l'utilisateur de quitter une association.
     * 
     * Supprime l'adhésion de l'utilisateur.
     *
     * @param string $id ID de l'association à quitter
     * @return \Illuminate\Http\RedirectResponse Redirection avec message de succès ou erreur
     */
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


