<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('audits')->cascadeOnDelete();
            $table->enum('statut', ['brouillon', 'final'])->default('brouillon');
            $table->unsignedTinyInteger('score')->nullable(); // score global /100, calculé ou saisi
            $table->timestamps();
        });

        Schema::create('constats_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapport_audit_id')->constrained('rapports_audit')->cascadeOnDelete();
            $table->enum('type', ['point_sensible', 'ecart_mineur', 'ecart_majeur', 'recommandation']);
            $table->string('reference_norme')->nullable(); // ex: "8.5.1"
            $table->text('description');
            $table->foreignId('action_corrective_id')->nullable()->constrained('actions')->nullOnDelete(); // suivi via module Tâches/Projet
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('constats_audit');
        Schema::dropIfExists('rapports_audit');
    }
};
