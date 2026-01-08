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
        Schema::create('commentaire', function (Blueprint $table) {
            $table->id('idCommentaire');
            $table->unsignedBigInteger('idUser');
            $table->string('descCommentaire', 255);
            $table->string('idAssociation', 255);
            $table->integer('noteAssociation');
            $table->timestamps();

            $table->index('idUser');
            $table->index('idAssociation');
            
            $table->foreign('idUser')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commentaire');
    }
};
