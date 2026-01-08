<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    $commentaires = Commentaire::where('idUser', Auth::id())
        ->orderBy('idCommentaire', 'desc')
        ->get();
    
    $associations = \App\Models\MembreAsso::where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();
    
    return view('dashboard', [
        'commentaires' => $commentaires,
        'associations' => $associations
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// routes pour la page de recherche , la page de detail et la reinitialisation des filtres

Route::get('/recherche-associations', [AssociationController::class, 'search'])->name('recherche.associations');
Route::get('/association/{id}', [AssociationController::class, 'show'])->name('association.show') ;
Route::get('/reinitialiser-filtres', [AssociationController::class, 'reinitialiserFiltres'])->name('reinitialiser.filtres');

// Routes pour les commentaires
Route::post('/commentaire/ajouter', [AssociationController::class, 'ajouterCommentaire'])->middleware('auth')->name('commentaire.ajouter');
Route::delete('/commentaire/{id}', [AssociationController::class, 'supprimerCommentaire'])->middleware('auth')->name('commentaire.supprimer');

// Routes pour rejoindre/quitter une association
Route::post('/association/{id}/rejoindre', [AssociationController::class, 'rejoindre'])->middleware('auth')->name('association.rejoindre');
Route::delete('/association/{id}/quitter', [AssociationController::class, 'quitter'])->middleware('auth')->name('association.quitter');




require __DIR__.'/auth.php';
