<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Informations Association</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- importation de  Leaflet pour faire la carte OpenStreetMap-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
     <style>
        body {
            background-color: #fff;
            color: black;
            font-family: "Raleway", sans-serif;
            font-weight: 500;
        }
    </style>
</head>
<body class="p-10 bg-gray-100">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">
        <a href="{{ route('recherche.associations') }}" class="inline-block mb-6 text-black-600 hover:text-black-800 font-semibold">&larr; Retour à la recherche</a>


        <!-- Vérification si l'association existe -->
        @if($association)
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-3xl font-bold text-black-600">{{ $association['title'] ?? 'Nom inconnu' }}</h1>
                
                @auth
                    @if($estMembre)
                        <form method="POST" action="{{ route('association.quitter', $association['id']) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded transition">
                                Quitter l'association
                            </button>
                        </form>
                    @elseif($adhesion && $adhesion->status === 'pending')
                        <button disabled class="bg-gray-400 text-white font-bold py-2 px-6 rounded cursor-not-allowed">
                            Demande en attente
                        </button>
                    @else
                        <form method="POST" action="{{ route('association.rejoindre', $association['id']) }}">
                            @csrf
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                                Rejoindre l'association
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition">
                        Connexion pour rejoindre
                    </a>
                @endauth
            </div>
            
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-black-500">
                <h3 class="font-bold text-lg mb-2 text-gray-800">Objet de l'association</h3>
                <p class="text-gray-700 italic">
                    {{ $association['object'] ?? 'Pas de description disponible.' }}
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-lg mb-2 border-b pb-1">Coordonnées</h3>
                    <p class="text-gray-700">
                        <strong>Adresse :</strong><br>
                       
                       {{$association['street_number_asso'] ?? ''}} {{ $association['street_type_asso'] ?? '' }} {{ $association['street_name_asso'] ?? '' }}<br>
                   {{ $association['pc_address_asso'] ?? '' }} {{ $association['com_name_asso'] ?? '' }}<br> </p>
                </div>
                
                <div>
                    <h3 class="font-bold text-lg mb-2 border-b pb-1">Informations administratives</h3>
                    <ul class="text-gray-700 space-y-1">
                        <li><strong>RNA / ID :</strong> {{ $association['id'] ?? $association['id_association'] ?? 'N/A' }}</li>
                        <li><strong>SIRET :</strong> {{ $association['siret'] ?? 'Non renseigné' }}</li>
                        <li><strong>Date de création :</strong> {{ $association['creation_date'] ?? 'N/C' }}</li>
                        <li><strong>Date de publication :</strong> {{ $association['publication_date'] ?? 'N/C' }}</li>
                    </ul>
                </div>
            </div>

            @if(isset($association['geo_point_2d']['lat']) && isset($association['geo_point_2d']['lon']))
            <div class="mt-8">
                <h3 class="font-bold text-lg mb-2 border-b pb-1">Localisation</h3>
                <!-- Map Container -->
                <div id="map" class="h-64 md:h-96 w-full rounded-lg shadow border z-0"></div>
            </div>
            @endif

            <!-- Section Commentaires -->
            <div class="mt-8">
                <h3 class="font-bold text-2xl mb-4 border-b-2 pb-2">Avis ({{ $commentaires->count() }})</h3>
                
                @if($moyenneNote)
                    <div class="mb-4 flex items-center">
                        <span class="text-3xl font-bold text-yellow-500">{{ number_format($moyenneNote, 1) }}</span>
                        <span class="ml-2 text-gray-600">/ 5</span>
                        <span class="ml-4 text-gray-500">({{ $commentaires->count() }} avis)</span>
                    </div>
                @endif

                <!-- Messages de succès/erreur -->
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Formulaire d'ajout d'avis -->
                @auth
                    @if(!$aDejaCommente)
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h4 class="font-bold mb-3">Laisser un avis</h4>
                            <form action="{{ route('commentaire.ajouter') }}" method="POST">
                                @csrf
                                <input type="hidden" name="idAssociation" value="{{ $association['id'] }}">
                                
                                <div class="mb-3">
                                    <label class="block text-sm font-medium mb-1">Note</label>
                                    <select name="noteAssociation" required class="w-full p-2 border rounded">
                                        <option value="">Choisir une note</option>
                                        <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                                        <option value="4">⭐⭐⭐⭐ (4/5)</option>
                                        <option value="3">⭐⭐⭐ (3/5)</option>
                                        <option value="2">⭐⭐ (2/5)</option>
                                        <option value="1">⭐ (1/5)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="block text-sm font-medium mb-1">Votre avis</label>
                                    <textarea name="descCommentaire" required maxlength="500" rows="4" 
                                        class="w-full p-2 border rounded" 
                                        placeholder="Partagez votre expérience..."></textarea>
                                </div>

                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                    Publier mon avis
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-gray-100 rounded-lg text-gray-600">
                            Vous avez déjà laissé un avis pour cette association.
                        </div>
                    @endif
                @else
                    <div class="mb-6 p-4 bg-gray-100 rounded-lg">
                        <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Connectez-vous</a> pour laisser un avis.
                    </div>
                @endauth

                <!-- Liste des commentaires -->
                @if($commentaires->count() > 0)
                    <div class="space-y-4">
                        @foreach($commentaires as $commentaire)
                            <div class="border rounded-lg p-4 bg-white shadow-sm">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-semibold">{{ $commentaire->user->name }}</span>
                                        <span class="text-yellow-500 ml-2">
                                            @for($i = 0; $i < $commentaire->noteAssociation; $i++)
                                                ⭐
                                            @endfor
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @auth
                                            @if($commentaire->idUser === Auth::id())
                                                <form action="{{ route('commentaire.supprimer', $commentaire->idCommentaire) }}" method="POST" 
                                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 text-sm hover:underline">Supprimer</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                <p class="text-gray-700">{{ $commentaire->descCommentaire }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">Aucun avis pour le moment. Soyez le premier à donner votre avis !</p>
                @endif
            </div>

        @else
            <div class="text-center py-10">
                <h2 class="text-2xl font-bold text-red-600 mb-2">Association introuvable</h2>
                <p class="text-gray-600">Impossible de récupérer les informations demandées.</p>
            </div>
        @endif
    </div>
 

    <script>
        // script pour faire fonctionner la carte 
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de la carte par défaut sinon non fonctionnelle 
            var map = L.map('map').setView([46.603354, 1.888334], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

           
            // On récupère lat et lon depuis les données de l'association
            var lat = "{{ $association['geo_point_2d']['lat'] ?? '' }}";
            var lon = "{{ $association['geo_point_2d']['lon'] ?? '' }}";
            
            if (lat && lon) {
                map.setView([lat, lon], 15);
                L.marker([lat, lon]).addTo(map)
                    .bindPopup("<b>{{ $association['title'] ?? 'Association' }}</b>")
                    .openPopup();
            }
        });
    </script>
</body>
</html>