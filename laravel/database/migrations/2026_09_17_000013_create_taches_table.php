<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_echeance')->nullable();
            $table->string('responsable')->nullable();
            $table->enum('statut', ['a_faire', 'en_cours', 'faite'])->default('a_faire');
            $table->nullableMorphs('liee'); // liee_type/liee_id : Projet, Audit, Formation, ou nul si tâche libre
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
