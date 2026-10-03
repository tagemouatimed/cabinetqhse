<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->foreign('projet_id')->references('id')->on('projets')->nullOnDelete();
        });

        Schema::table('echeances_paiement', function (Blueprint $table) {
            $table->foreign('projet_id')->references('id')->on('projets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropForeign(['projet_id']);
        });
        Schema::table('echeances_paiement', function (Blueprint $table) {
            $table->dropForeign(['projet_id']);
        });
    }
};
