
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Recherche Associations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-10 bg-gray-100">

    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-6 text-blue-600">Annuaire des Associations</h1>

        <form action="{{ route('associations.search') }}" method="GET" class="mb-8">
            <div class="flex gap-2">
                <input 
                    type="text" 
                    name="query" 
                    value="{{ $query ?? '' }}" 
                    placeholder="Rechercher une association..." 
                    class="w-full p-3 rounded border border-gray-300 shadow-sm focus:ring-2 focus:ring-blue-500"
                >
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 font-bold">
                    Rechercher
                </button>
            </div>
        </form>

        <div class="space-y-4">
            @if(isset($results) && count($results) > 0)
                @foreach($results as $asso)
                    <div class="bg-white p-5 rounded shadow hover:shadow-md transition">
                        <h2 class="text-xl font-bold text-gray-800">{{ $asso['title'] ?? 'Nom inconnu' }}</h2>
                        <p class="text-gray-600 text-sm mb-2">
                            📍 {{ $asso['street_name_asso'] ?? 'Adresse non renseignée' }} 
                            ({{ $asso['pc_address_asso'] ?? 'CP' }})
                        </p>
                        <p class="text-gray-700 italic">
                            {{ Str::limit($asso['object'] ?? 'Pas de description', 150) }}
                        </p>
                    </div>
                @endforeach
            @elseif($query)
                <div class="text-center text-gray-500">Aucune association trouvée pour "{{ $query }}".</div>
            @endif
        </div>
    </div>

    {{ $results->links() }}

</body>
</html>