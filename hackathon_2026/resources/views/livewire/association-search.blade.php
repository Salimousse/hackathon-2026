<div>
    <div class="mb-8 bg-white p-6 rounded shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-4">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="query">Recherche</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="query"
                    placeholder="Rechercher une assocaiation ..." 
                    class="w-full p-3 rounded border border-black-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>
          <!--Ajout de filtre par ville et code postal  -->
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="ville">Ville</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="ville"
                    placeholder="Ex: Paris" 
                    class="w-full p-3 rounded border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="md:col-span-1">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="cp">Code Postal</label>
                <input 
                    type="text" 
                    wire:model.live.debounce.500ms="cp"
                    placeholder="Ex: 75001" 
                    class="w-full p-3 rounded border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div class="md:col-span-1 flex items-end">
                <button 
                    type="button"
                    wire:click="$refresh"
                    class="w-full bg-black text-white px-6 py-3 rounded hover:bg-gray-800 font-bold transition">
                    Rechercher
                </button>
            </div>
        </div>

        <button 
            type="button"
            wire:click="$set('query', ''); $set('ville', ''); $set('cp', ''); $set('lat', ''); $set('lon', '')"
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
                        <h2 class="text-xl font-bold text-black">{{ $asso['title'] ?? 'Nom inconnu' }}</h2>
                        <p class="text-black text-sm mb-2">
                            {{ $asso['com_name_asso'] ?? 'Adresse non renseignée' }} 
                            ({{ $asso['pc_address_asso'] ?? 'CP' }})
                        </p>
                        <p class="text-black italic">
                            {{ Str::limit($asso['object'] ?? 'Pas de description', 150) }}
                        </p>
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