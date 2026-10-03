<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->string('theme');
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete(); // vide si formation interne
            $table->string('formateur')->nullable();
            $table->date('date_planifiee');
            $table->unsignedSmallInteger('duree_heures')->nullable();
            $table->enum('statut', ['planifiee', 'realisee', 'annulee'])->default('planifiee');
            $table->timestamps();
        });

        Schema::create('participants_formation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->cascadeOnDelete();
            $table->string('nom');
            $table->string('prenom');
            $table->boolean('present')->default(false);
            $table->unsignedTinyInteger('note_satisfaction')->nullable(); // évaluation à chaud /10
            $table->timestamps();
        });

        Schema::create('certificats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_formation_id')->constrained('participants_formation')->cascadeOnDelete();
            $table->string('numero')->unique(); // ex: CERT01/26
            $table->date('date_delivrance');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificats');
        Schema::dropIfExists('participants_formation');
        Schema::dropIfExists('formations');
    }
};
