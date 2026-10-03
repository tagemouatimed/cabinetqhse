<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->decimal('frais_deplacement', 12, 2)->nullable()->after('montant_ht');
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->decimal('frais_deplacement', 12, 2)->nullable()->after('montant_ht');
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn('frais_deplacement');
        });
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn('frais_deplacement');
        });
    }
};
