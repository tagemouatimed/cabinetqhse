<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans_action', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->string('titre');
            $table->foreignId('phase_id')->nullable(); // lien vers phases_accompagnement (paramétrage), FK ajoutée plus tard
            $table->timestamps();
        });

        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_action_id')->constrained('plans_action')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_echeance')->nullable();
            $table->string('responsable')->nullable();
            $table->enum('statut', ['a_faire', 'en_cours', 'realisee', 'en_retard'])->default('a_faire');
            $table->timestamps();
        });

        Schema::create('notes_projet', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->text('contenu');
            $table->string('auteur')->nullable();
            $table->timestamps();
        });

        Schema::create('rapports_reunion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->date('date_reunion');
            $table->string('objet')->nullable();
            $table->text('compte_rendu');
            $table->timestamps();
        });

        // Pièces jointes génériques, réutilisées par tous les modules (projet, audit, formation...)
        Schema::create('pieces_jointes', function (Blueprint $table) {
            $table->id();
            $table->morphs('attachable'); // attachable_type, attachable_id
            $table->string('chemin_fichier'); // toujours sur disque privé ('local')
            $table->string('nom_original');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pieces_jointes');
        Schema::dropIfExists('rapports_reunion');
        Schema::dropIfExists('notes_projet');
        Schema::dropIfExists('actions');
        Schema::dropIfExists('plans_action');
    }
};
