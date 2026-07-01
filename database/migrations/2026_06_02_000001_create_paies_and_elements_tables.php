<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("paies", function (Blueprint $table) {
            $table->id();
            $table->foreignId("salarie_id")->nullable();
            $table->integer("mois");
            $table->integer("annee");
            $table->double("salaire_base")->default(0);
            $table->double("montant_primes")->default(0);
            $table->double("montant_retenues")->default(0);
            $table->double("salaire_net")->default(0);
            $table
                ->enum("statut", ["brouillon", "valide", "paye"])
                ->default("brouillon");
            $table->date("date_paiement")->nullable();
            $table->string("mode_paiement")->nullable(); // Espece, Virement, Cheque
            $table->foreignId("tresorerie_id")->nullable();
            $table->foreignId("user_id")->nullable(); // Créé par
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create("paie_elements", function (Blueprint $table) {
            $table->id();
            $table->foreignId("paie_id")->nullable();
            $table->enum("type", ["prime", "retenue"]);
            $table->string("libelle");
            $table->double("montant");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("paie_elements");
        Schema::dropIfExists("paies");
    }
};
