<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('projet_id')->nullable()->constrained('projets')->nullOnDelete();
            $table->foreignId('norme_id')->constrained('normes');
            $table->enum('type', ['recurrent', 'ponctuel'])->default('ponctuel');
            $table->date('date_planifiee');
            $table->unsignedSmallInteger('recurrence_mois')->nullable(); // ex: 12 = annuel, si type=recurrent
            $table->enum('statut', ['planifie', 'realise', 'reporte', 'annule'])->default('planifie');
            $table->timestamps();
        });

        // Plan d'audit standard par norme (modèle), dupliqué et adapté par audit
        Schema::create('plans_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('norme_id')->nullable()->constrained('normes')->nullOnDelete(); // rempli si modèle standard
            $table->foreignId('audit_id')->nullable()->constrained('audits')->cascadeOnDelete(); // rempli si adapté à un audit précis
            $table->string('nom');
            $table->boolean('est_modele')->default(false);
            $table->timestamps();
        });

        Schema::create('plan_audit_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_audit_id')->constrained('plans_audit')->cascadeOnDelete();
            $table->string('processus_audite');
            $table->unsignedSmallInteger('duree_minutes')->nullable();
            $table->string('auditeur')->nullable();
            $table->string('audite')->nullable();
            $table->dateTime('date_prevue')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_audit_lignes');
        Schema::dropIfExists('plans_audit');
        Schema::dropIfExists('audits');
    }
};
