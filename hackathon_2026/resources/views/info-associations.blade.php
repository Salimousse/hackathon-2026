<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Informations Association</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- importation de  Leaflet pour faire la carte OpenStreetMap-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="p-10 bg-gray-100">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">
        <a href="{{ route('recherche.associations') }}" class="inline-block mb-6 text-blue-600 hover:text-blue-800 font-semibold">&larr; Retour à la recherche</a>


        <!-- Vérification si l'association existe -->
        @if($association)
            <h1 class="text-3xl font-bold text-blue-600 mb-4">{{ $association['title'] ?? 'Nom inconnu' }}</h1>
            
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
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