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
        Schema::create('info_societes', function (Blueprint $table) {
            $table->id();
            $table->string('nom_societe')->nullable();
            $table->string('adresse_societe')->nullable();
            $table->string('telephone_societe')->nullable();
            $table->string('email_societe')->nullable();
            $table->string('nif_societe')->nullable();
            $table->string('logo_societe')->nullable();
            $table->string('stat_societe')->nullable();
            $table->string('rcs_societe')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('info_societes');
    }
};
