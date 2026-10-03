<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances_paiement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('projet_id')->nullable(); // lien module Projet (contrainte ajoutée quand la table projets existe)
            $table->foreignId('facture_id')->nullable()->constrained('factures')->nullOnDelete();
            $table->string('partie'); // ex: "1ère tranche", "Acompte", "Solde"
            $table->date('date_echeance');
            $table->decimal('montant_prevu', 12, 2);
            $table->boolean('reglement_recu')->default(false);
            $table->date('date_reglement')->nullable();
            $table->enum('moyen_paiement', ['virement', 'cheque', 'espece', 'traite', 'autre'])->nullable();
            $table->decimal('montant_regle', 12, 2)->default(0);
            // "reste" n'est pas stocké : calculé (montant_prevu - montant_regle) via accessor du modèle
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echeances_paiement');
    }
};
