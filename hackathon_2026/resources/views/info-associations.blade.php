<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Informations Association</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<body class="p-10 bg-gray-100">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded shadow">
        <a href="{{ route('recherche.associations') }}" class="inline-block mb-6 text-blue-600 hover:text-blue-800 font-semibold">&larr; Retour à la recherche</a>

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

            
        @else
            <div class="text-center py-10">
                <h2 class="text-2xl font-bold text-red-600 mb-2">Association introuvable</h2>
                <p class="text-gray-600">Impossible de récupérer les informations demandées.</p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation de la carte (vue par défaut sur Paris)
            var map = L.map('map').setView([46.603354, 1.888334], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Récupération de l'adresse propre
            var address = "{{ ($association['street_name_asso'] ?? '') . ' ' . ($association['pc_address_asso'] ?? '') . ' ' . ($association['city_address_asso'] ?? '') }}";
            
            if(address.trim() !== "") {
                // Appel à l'API Nominatim pour géocoder l'adresse
                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
                    .then(response => response.json())
                    .then(data => {
                        if(data.length > 0) {
                            var lat = data[0].lat;
                            var lon = data[0].lon;
                            
                            // Mise à jour de la carte avec la position trouvée
                            map.setView([lat, lon], 15);
                            L.marker([lat, lon]).addTo(map)
                                .bindPopup("<b>{{ $association['title'] ?? 'Association' }}</b><br>" + address)
                                .openPopup();
                        }
                    })
                    .catch(error => console.log('Erreur de géocodage:', error));
            }
        });
    </script>
</body>
</html>