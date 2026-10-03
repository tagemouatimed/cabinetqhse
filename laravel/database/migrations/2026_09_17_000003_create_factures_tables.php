<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique(); // ex: CO01/26
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('devis_id')->nullable()->constrained('devis')->nullOnDelete();
            $table->foreignId('projet_id')->nullable(); // lien module Projet (contrainte ajoutée quand la table projets existe)
            $table->date('date_facture');
            $table->date('date_echeance')->nullable();
            $table->enum('statut', ['brouillon', 'envoyee', 'payee', 'partiellement_payee', 'en_retard', 'annulee'])->default('brouillon');
            $table->decimal('montant_ht', 12, 2)->default(0);
            $table->decimal('taux_tva', 5, 2)->default(20);
            $table->decimal('montant_ttc', 12, 2)->default(0);
            $table->decimal('montant_regle', 12, 2)->default(0);
            $table->boolean('est_avoir')->default(false);
            $table->foreignId('facture_avoir_de_id')->nullable()->constrained('factures')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('facture_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facture_id')->constrained('factures')->cascadeOnDelete();
            $table->foreignId('prestation_id')->nullable()->constrained('prestations')->nullOnDelete();
            $table->string('designation');
            $table->decimal('quantite', 10, 2)->default(1);
            $table->decimal('prix_unitaire', 12, 2);
            $table->decimal('montant', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facture_lignes');
        Schema::dropIfExists('factures');
    }
};
