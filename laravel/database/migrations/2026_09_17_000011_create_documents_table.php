<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->enum('categorie', ['norme', 'support_formation', 'autre']);
            $table->string('titre');
            $table->string('chemin_fichier'); // disque privé
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('document_precedent_id')->nullable()->constrained('documents')->nullOnDelete(); // historique de versions
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
