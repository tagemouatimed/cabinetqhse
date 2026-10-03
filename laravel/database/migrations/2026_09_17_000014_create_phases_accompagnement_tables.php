<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phases_accompagnement', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('actions_preetablies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('phase_id')->constrained('phases_accompagnement')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Contrainte différée : plans_action.phase_id -> phases_accompagnement (table créée après plans_action, cf. §4 du rapport : vérifier avant d'ajouter une FK)
        Schema::table('plans_action', function (Blueprint $table) {
            $table->foreign('phase_id')->references('id')->on('phases_accompagnement')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('plans_action', function (Blueprint $table) {
            $table->dropForeign(['phase_id']);
        });
        Schema::dropIfExists('actions_preetablies');
        Schema::dropIfExists('phases_accompagnement');
    }
};
