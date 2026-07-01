<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $blueprint) {
            $blueprint->enum('sexe', ['M', 'F'])->nullable()->after('prenom');
            $blueprint->date('date_naissance')->nullable()->after('sexe');
            $blueprint->string('lieu_naissance')->nullable()->after('date_naissance');
            $blueprint->string('nationalite')->nullable()->after('lieu_naissance');
            $blueprint->date('date_cin')->nullable()->after('cin');
            $blueprint->string('lieu_cin')->nullable()->after('date_cin');
            $blueprint->string('photo')->nullable()->after('adresse');
            $blueprint->enum('statut', ['actif', 'inactif', 'suspendu'])->default('actif')->after('date_embauche');
            $blueprint->text('observation')->nullable()->after('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $blueprint) {
            $blueprint->dropColumn([
                'sexe',
                'date_naissance',
                'lieu_naissance',
                'nationalite',
                'date_cin',
                'lieu_cin',
                'photo',
                'statut',
                'observation',
            ]);
        });
    }
};
