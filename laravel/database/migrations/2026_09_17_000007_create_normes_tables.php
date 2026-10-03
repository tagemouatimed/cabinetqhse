<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('normes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // ex: ISO 9001:2015
            $table->string('libelle');
            $table->timestamps();
        });

        Schema::create('exigences_norme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('norme_id')->constrained('normes')->cascadeOnDelete();
            $table->string('reference'); // ex: "8.5.1"
            $table->text('libelle');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exigences_norme');
        Schema::dropIfExists('normes');
    }
};
