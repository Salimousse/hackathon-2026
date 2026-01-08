<div>
    <div class="mb-8 bg-gray-100 p-6 shadow-md border-2 border-gray-300">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-4">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="query">Recherche</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="query"
                    placeholder="Rechercher une assocaiation ..." 
                    class="w-full p-3 border-2 border-gray-300 bg-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-gray-400"
                >
            </div>
          <!--Ajout de filtre par ville et code postal  -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="ville">Ville</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="ville"
                    placeholder="Ex: Paris" 
                    class="w-full p-3 border-2 border-gray-300 bg-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-gray-400"
                >
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="cp">Code Postal</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="cp"
                    placeholder="Ex: 75001" 
                    class="w-full p-3 border-2 border-gray-300 bg-gray-100 shadow-sm focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-gray-400"
                >
            </div>

            <div class="md:col-span-1 flex items-end">
                <button 
                    type="button"
                    wire:click="$refresh"
                    class="w-full bg-black text-white px-6 py-3 hover:bg-gray-800 font-bold transition">
                    Rechercher
                </button>
            </div>
        </div>

        <button 
            type="button"
            wire:click="resetFilters"
            class="mt-4 text-sm text-gray-500 underline ml-4">
            Réinitialiser les filtres
        </button>

        <div wire:loading class="text-black text-sm mt-2 font-semibold">
             Recherche en cours...
        </div>
    </div>

    <div class="space-y-4">
        @if(count($results) > 0)
            @foreach($results as $asso)
                @php
                    $id = $asso['id'] ?? null;
                @endphp
                
                @if($id)
                <a href="{{ route('association.show', $id) }}" class="block no-underline">
                @endif
                    <div class="bg-white p-5 rounded shadow hover:shadow-md transition cursor-pointer hover:bg-blue-50">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h2 class="text-xl font-bold text-black">{{ $asso['title'] ?? 'Nom inconnu' }}</h2>
                                <p class="text-black text-sm mb-2">
                                    {{ $asso['com_name_asso'] ?? 'Adresse non renseignée' }} 
                                    ({{ $asso['pc_address_asso'] ?? 'CP' }})
                                </p>
                                <p class="text-black italic">
                                    {{ Str::limit($asso['object'] ?? 'Pas de description', 150) }}
                                </p>
                            </div>
                            @if(isset($asso['distance_km']))
                                <div class="ml-4 flex-shrink-0">
                                    <div class="bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-center">
                                        <div class="text-2xl font-bold">{{ number_format($asso['distance_km'], 1) }}</div>
                                        <div class="text-xs">km</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @if($id)
                </a>
                @endif
            @endforeach

            <div class="mt-6">
                {{ $results->links() }}
            </div>
        @elseif($query || $ville || $cp)
            <div class="text-center text-gray-500">Aucune association trouvée.</div>
        @else
            <div class="text-center text-gray-500">Commencez votre recherche ci-dessus.</div>
        @endif
    </div>

    <script>
        function getLocation() {
            if (!navigator.geolocation) {
                alert("Géolocalisation non supportée par votre navigateur");
                return;
            }
            
            const button = event.target.closest('button');
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Localisation...';
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const component = window.Livewire.find('{{ $_instance->getId() }}');
                    component.set('lat', position.coords.latitude);
                    component.set('lon', position.coords.longitude);
                    button.textContent = 'Position trouvée !';
                    setTimeout(() => {
                        button.disabled = false;
                        button.textContent = originalText;
                    }, 2000);
                },
                (error) => {
                    button.disabled = false;
                    button.textContent = originalText;
                    let errorMsg = 'Impossible d\'obtenir votre position. ';
                    if (error.code === 1) {
                        errorMsg += 'Veuillez autoriser l\'accès à la géolocalisation.';
                    } else if (error.code === 2) {
                        errorMsg += 'Position non disponible.';
                    } else if (error.code === 3) {
                        errorMsg += 'Délai d\'attente dépassé.';
                    }
                    alert(errorMsg);
                },
                {
                    enableHighAccuracy: false,
                    timeout: 30000,
                    maximumAge: 300000
                }
            );
        }
    </script>
</div>