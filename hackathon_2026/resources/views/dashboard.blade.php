<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Dashboard</title>
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
<body class="p-10 bg-gray-100">

<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('welcome') }}" class="text-black-600 hover:text-black-800 font-semibold">&larr; Retour à l'accueil</a>
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">
                Déconnexion
            </button>
        </form>
    </div>

    <!-- Section Bienvenue -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="text-2xl font-bold mb-2">Bienvenue, {{ Auth::user()->name }} !</h3>
        <p class="text-gray-600">Voici vos associations et avis.</p>
    </div>

    <!-- Section Mes Associations -->
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">
             Mes Associations ({{ count($associations) }})
        </h3>

        @if(count($associations) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($associations as $item)
                    @php
                        $adhesion = $item['adhesion'];
                        $info = $item['info'];
                    @endphp
                    <div class="border rounded-lg p-4 bg-gray-50 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <span class="font-semibold text-gray-900 block mb-1">
                                    @if($info && isset($info['title']))
                                        {{ $info['title'] }}
                                    @else
                                        Association #{{ $adhesion->association_id }}
                                    @endif
                                </span>
                                @if($info && isset($info['com_name_asso']))
                                    <p class="text-xs text-gray-600 mb-2">
                                        {{ $info['com_name_asso'] }} ({{ $info['pc_address_asso'] ?? '' }})
                                    </p>
                                @endif
                                <div class="flex gap-2 items-center">
                                    <span class="text-xs px-2 py-1 rounded
                                        @if($adhesion->status === 'accepted') bg-green-100 text-green-800
                                        @elseif($adhesion->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        @if($adhesion->status === 'accepted') Membre
                                        @elseif($adhesion->status === 'pending') En attente
                                        @else Refusé
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        Rôle: {{ ucfirst($adhesion->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('association.show', $adhesion->association_id) }}" 
                               class="flex-1 text-center bg-blue-600 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 transition">
                                Voir l'association
                            </a>
                            @if($adhesion->status === 'accepted')
                                <form method="POST" action="{{ route('association.quitter', $adhesion->association_id) }}" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir quitter cette association ?')"
                                            class="w-full bg-red-500 text-white text-sm px-3 py-2 rounded hover:bg-red-600 transition">
                                        Quitter
                                    </button>
                                </form>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            Rejoint le {{ $adhesion->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">
                    Vous n'avez rejoint aucune association pour le moment.
                </p>
                <a href="{{ route('recherche.associations') }}" 
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Découvrir des associations
                </a>
            </div>
        @endif
    </div>

    <!-- Section Mes Avis -->
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-xl font-bold text-gray-900 mb-4">
            💬 Mes Avis ({{ $commentaires->count() }})
        </h3>

        @if($commentaires->count() > 0)
            <div class="space-y-4">
                @foreach($commentaires as $commentaire)
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-semibold text-gray-900">
                                    {{ $commentaire->nomAssociation }}
                                </span>
                                <span class="text-yellow-500 ml-2">
                                    @for($i = 0; $i < $commentaire->noteAssociation; $i++)
                                        ⭐
                                    @endfor
                                    ({{ $commentaire->noteAssociation }}/5)
                                </span>
                            </div>
                            <a href="{{ route('association.show', $commentaire->idAssociation) }}" 
                               class="text-blue-600 text-sm hover:underline">
                                Voir l'association
                            </a>
                        </div>
                        <p class="text-gray-700 mt-2">{{ $commentaire->descCommentaire }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500 mb-4">
                    Vous n'avez encore laissé aucun avis.
                </p>
                <a href="{{ route('recherche.associations') }}" 
                   class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Découvrir des associations
                </a>
            </div>
        @endif
    </div>
</div>

</body>
</html>
