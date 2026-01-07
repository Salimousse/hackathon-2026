<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssociationController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
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



require __DIR__.'/auth.php';
