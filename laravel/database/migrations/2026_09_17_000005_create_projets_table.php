<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->string('nom');
            $table->string('norme')->nullable(); // ex: ISO 9001, ISO 45001...
            $table->string('secteur')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'en_retard', 'termine', 'suspendu'])->default('planifie');
            $table->unsignedTinyInteger('avancement')->default(0); // % calculé depuis les actions
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
