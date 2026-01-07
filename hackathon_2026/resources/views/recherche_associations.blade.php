<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recherche Associations</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #fff;
            color: black;
            font-family: "Raleway", sans-serif;
            font-weight: 100;
        }
    </style>
</head>
<body class="p-10">

<div class="max-w-3xl mx-auto">
            <a href="{{ route('welcome') }}" class="inline-block mb-6 text-black-600 hover:text-black-800 font-semibold">&larr; Retour à la page d'accueil</a>

    <h1 class="text-3xl font-bold mb-6 text-black-600">Annuaire des Associations</h1>

    <form action="{{ route('recherche.associations') }}" method="GET" class="mb-8 bg-white p-6 rounded shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-4">
                <label class="block text-sm font-bold mb-1 text-gray-700" for="query">Recherche</label>
                <input 
                    type="text" 
                    id="query"
                    name="query" 
                    value="{{ request('query') }}" 
                    placeholder="Nom ou objet de l'association..." 
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

            <div class="md:col-span-1">
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

            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full bg-black text-white px-6 py-3 rounded hover:bg-gray-800 font-bold transition">
                    Rechercher
                </button>
            </div>
        </div>

        <a href="{{ route('reinitialiser.filtres') }}" class="mt-4 text-sm text-gray-500 underline ml-4">Réinitialiser les filtres</a>

    </form>

    <div class="space-y-4">
        @if(isset($results) && count($results) > 0)
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
        @elseif($query)
            <div class="text-center text-gray-500">Aucune association trouvée pour "{{ $query }}".</div>
        @endif
    </div>

    
        <div class="mt-6">
            {{ $results->links() }}
        </div>
   
</div>

</body>
</html>