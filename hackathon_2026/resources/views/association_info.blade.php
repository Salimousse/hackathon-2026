<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Détails de l'association
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if(isset($association))
                        <div class="space-y-6">
                            <!-- Titre de l'association -->
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                                    {{ $association['record']['fields']['titre'] ?? 'Sans titre' }}
                                </h3>
                            </div>

                            <!-- Informations principales -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Numéro RNA -->
                                @if(isset($association['record']['fields']['id_association']))
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                        Numéro RNA
                                    </h4>
                                    <p class="text-lg font-medium">
                                        {{ $association['record']['fields']['id_association'] }}
                                    </p>
                                </div>
                                @endif

                                <!-- Date de création -->
                                @if(isset($association['record']['fields']['date_creation']))
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                        Date de création
                                    </h4>
                                    <p class="text-lg font-medium">
                                        {{ \Carbon\Carbon::parse($association['record']['fields']['date_creation'])->format('d/m/Y') }}
                                    </p>
                                </div>
                                @endif

                                <!-- Adresse -->
                                @if(isset($association['record']['fields']['adresse_libelle_voie']))
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                        Adresse
                                    </h4>
                                    <p class="text-lg font-medium">
                                        {{ $association['record']['fields']['adresse_libelle_voie'] ?? '' }}
                                        <br>
                                        {{ $association['record']['fields']['adresse_code_postal'] ?? '' }} 
                                        {{ $association['record']['fields']['adresse_libelle_commune'] ?? '' }}
                                    </p>
                                </div>
                                @endif

                                <!-- Nature -->
                                @if(isset($association['record']['fields']['nature']))
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                        Nature
                                    </h4>
                                    <p class="text-lg font-medium">
                                        {{ $association['record']['fields']['nature'] }}
                                    </p>
                                </div>
                                @endif

                                <!-- Groupement -->
                                @if(isset($association['record']['fields']['groupement']))
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                        Groupement
                                    </h4>
                                    <p class="text-lg font-medium">
                                        {{ $association['record']['fields']['groupement'] }}
                                    </p>
                                </div>
                                @endif

                                <!-- Position -->
                                @if(isset($association['record']['fields']['position']))
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-1">
                                        Position
                                    </h4>
                                    <p class="text-lg font-medium">
                                        {{ $association['record']['fields']['position'] }}
                                    </p>
                                </div>
                                @endif
                            </div>

                            <!-- Objet de l'association -->
                            @if(isset($association['record']['fields']['objet']))
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg border border-blue-200 dark:border-blue-800">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                                    Objet de l'association
                                </h4>
                                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ $association['record']['fields']['objet'] }}
                                </p>
                            </div>
                            @endif

                            <!-- Bouton retour -->
                            <div class="mt-8">
                                <a href="{{ route('associations.search') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    ← Retour à la recherche
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400 text-lg">
                                Aucune information disponible pour cette association.
                            </p>
                            <a href="{{ route('associations.search') }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white">
                                Retour à la recherche
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
