<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recherche Associations</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700" rel="stylesheet" type="text/css">
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        body {
            background-color: #fff;
            color: black;
            font-family: "Raleway", sans-serif;
            font-weight: 500;
        }
    </style>
</head>
<body class="p-10">

<div class="max-w-3xl mx-auto">
    <a href="{{ route('welcome') }}" class="inline-block mb-6 text-black-600 hover:text-black-800 font-semibold">&larr; Retour à la page d'accueil</a>

    <h1 class="text-3xl font-bold mb-6 text-black-600">Annuaire des Associations</h1>

    <livewire:association-search />

</div>

@livewireScripts
</body>
</html>
</div>

</body>
</html>