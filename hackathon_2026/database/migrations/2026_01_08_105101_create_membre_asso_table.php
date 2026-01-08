<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membre_asso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('association_id', 255);
            $table->string('role', 50)->default('membre');
            $table->string('status', 50)->default('pending');
            $table->timestamps();
            
            // Index pour améliorer les performances de recherche
            $table->index('user_id');
            $table->index('association_id');
            
            // Empêcher un utilisateur de rejoindre deux fois la même association
            $table->unique(['user_id', 'association_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membre_asso');
    }
};
