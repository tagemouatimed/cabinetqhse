<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['pourcentage', 'nombre', 'autre'])->default('nombre');
            $table->enum('source', ['manuel', 'base'])->default('manuel');
            $table->string('cle_base')->nullable(); // clé d'une requête prédéfinie et whitelistée (voir IndicateurService), jamais de SQL libre saisi par l'utilisateur
            $table->enum('type_affichage', ['courbe', 'barres', 'jauge', 'carte'])->default('carte');
            $table->timestamps();
        });

        Schema::create('valeurs_indicateur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicateur_id')->constrained('indicateurs')->cascadeOnDelete();
            $table->string('periode'); // ex: "2026-09", "2026-T3", "2026"
            $table->decimal('valeur_d1', 14, 2)->nullable(); // numérateur, saisie manuelle
            $table->decimal('valeur_d2', 14, 2)->nullable(); // dénominateur, saisie manuelle (indicateurs %)
            $table->decimal('valeur', 14, 2)->nullable(); // valeur finale (calculée : D1/D2*100, ou saisie directe si type=nombre)
            $table->timestamps();
            $table->unique(['indicateur_id', 'periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valeurs_indicateur');
        Schema::dropIfExists('indicateurs');
    }
};
