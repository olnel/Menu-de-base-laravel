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
        Schema::create('salaries', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('matricule')->unique();
            $blueprint->string('nom');
            $blueprint->string('prenom')->nullable();
            $blueprint->string('email')->nullable();
            $blueprint->string('telephone')->nullable();
            $blueprint->string('adresse')->nullable();
            $blueprint->string('cin')->nullable();
            $blueprint->date('date_embauche')->nullable();
            $blueprint->foreignId('type_salarie_id');
            $blueprint->softDeletes();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
