@extends('layouts.base') @section('title', 'Bienvenue') @section('content')

<div class="title m-b-md">Constellation</div>
<div class="text-xl text-gray-700 mb-8" style="font-family: 'Raleway', sans-serif; font-weight: 400;">Votre réseau associatif local</div>

<div class="max-w-4xl mx-auto w-full px-4">
    <form action="{{ route('recherche.associations') }}" method="GET" class="mb-8 bg-white p-6 rounded shadow-lg border-2 border-gray-200">
        <!-- Champs cachés pour la géolocalisation -->
        <input type="hidden" name="lat" id="lat" value="">
        <input type="hidden" name="lon" id="lon" value="">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-4">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="query">Recherche</label>
                <input 
                    type="text" 
                    id="query"
                    name="query" 
                    value="{{ request('query') }}" 
                    placeholder="Rechercher une association ..." 
                    class="w-full p-3 rounded border border-black-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>
          <!--Ajout de filtre par ville et code postal  -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="ville">Ville</label>
                <input 
                    type="text" 
                    id="ville"
                    name="ville" 
                    value="{{ request('ville') }}" 
                    placeholder="Ex: Paris" 
                    class="w-full p-3 rounded border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="cp">Code Postal</label>
                <input 
                    type="text" 
                    id="cp"
                    name="cp" 
                    value="{{ request('cp') }}" 
                    placeholder="Ex: 75001" 
                    class="w-full p-3 rounded border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

       
            <div class="md:col-span-4 flex gap-3 mt-2">
                <button type="submit" class="flex-1 bg-black text-white px-6 py-3 rounded hover:bg-gray-800 font-bold transition">
                    Rechercher
                </button>
                
                <button 
                    type="button" 
                    id="geolocButton"
                    onclick="getGeolocation()"
                    class="flex-1 bg-black text-white px-6 py-3 rounded hover:bg-gray-900 font-bold transition flex items-center justify-center gap-2"
                >
                    
                    <span id="geolocText">Autour de moi</span>
                </button>
                
                <a 
                    href="{{ route('reinitialiser.filtres') }}" 
                    class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded hover:bg-gray-300 font-bold transition text-center flex items-center justify-center"
                >
                    Réinitialiser les filtres 
                </a>
            </div>
        </div>
    </form>
</div>

<script>
function getGeolocation() {
    console.log('Bouton géolocalisation cliqué');
    const button = document.getElementById('geolocButton');
    const text = document.getElementById('geolocText');
    
    if (!navigator.geolocation) {
        alert('La géolocalisation n\'est pas supportée par votre navigateur');
        console.error('Géolocalisation non supportée');
        return;
    }
    
    console.log('Géolocalisation supportée, demande en cours...');
    button.disabled = true;
    text.textContent = 'Localisation...';
    
    navigator.geolocation.getCurrentPosition(
        function(position) {
            console.log('Position obtenue:', position.coords.latitude, position.coords.longitude);
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lon').value = position.coords.longitude;
            
            text.textContent = 'Position trouvée !';
            
            setTimeout(function() {
                console.log('Soumission du formulaire...');
                document.querySelector('form').submit();
            }, 500);
        },
        function(error) {
            console.error('Erreur de géolocalisation:', error);
            button.disabled = false;
            text.textContent = 'Autour de moi';
            
            let errorMsg = 'Impossible d\'obtenir votre position. ';
            if (error.code === 1) {
                errorMsg += 'Veuillez autoriser l\'accès à votre position dans les paramètres du navigateur.';
            } else if (error.code === 2) {
                errorMsg += 'Position non disponible.';
            } else if (error.code === 3) {
                errorMsg += 'Délai d\'attente dépassé.';
            }
            errorMsg += '\n\nCode erreur: ' + error.code + '\nMessage: ' + error.message;
            alert(errorMsg);
        },
        {
            enableHighAccuracy: false,  // Plus rapide, moins précis mais suffisant
            timeout: 30000,              // 30 secondes au lieu de 10
            maximumAge: 300000           // Accepte une position de moins de 5 min
        }
    );
}
</script>

@endsection