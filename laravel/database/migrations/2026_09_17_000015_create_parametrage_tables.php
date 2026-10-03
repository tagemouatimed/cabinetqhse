<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajout du rôle sur la table users standard de Laravel (déjà présente dans qcore)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('utilisateur')->after('email');
        });

        // Matrice de permissions granulaire : par rôle, par module, par action
        // (plus fine que le MODULE_ACCESS de LabSysPro qui était par module seulement)
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');
            $table->string('module'); // ex: 'facturation', 'projets', 'audits'...
            $table->boolean('peut_voir')->default(false);
            $table->boolean('peut_ajouter')->default(false);
            $table->boolean('peut_modifier')->default(false);
            $table->boolean('peut_supprimer')->default(false);
            $table->boolean('peut_imprimer')->default(false);
            $table->timestamps();
            $table->unique(['role', 'module']);
        });

        // Couleur et ordre d'affichage des modules dans le menu
        Schema::create('parametres_interface', function (Blueprint $table) {
            $table->id();
            $table->string('module')->unique();
            $table->string('couleur')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_interface');
        Schema::dropIfExists('permissions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
