<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom')->nullable(); // vide si raison sociale (personne morale)
            $table->string('raison_sociale')->nullable();
            $table->string('adresse')->nullable();
            $table->string('ice')->nullable()->index();
            $table->string('secteur_activite')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
