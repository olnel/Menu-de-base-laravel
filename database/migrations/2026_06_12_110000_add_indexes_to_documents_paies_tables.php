<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
//        Schema::table('documents', function (Blueprint $table) {
//            $table->index('parent_id');
//            $table->index('document_type_id');
//        });

        Schema::table('paies', function (Blueprint $table) {
            $table->index('salarie_id');
            $table->index('tresorerie_id');
            $table->index('user_id');
        });

        Schema::table('paie_elements', function (Blueprint $table) {
            $table->index('paie_id');
        });
    }

    public function down(): void
    {
//        Schema::table('documents', function (Blueprint $table) {
//            $table->dropIndex(['parent_id']);
//            $table->dropIndex(['document_type_id']);
//        });

        Schema::table('paies', function (Blueprint $table) {
            $table->dropIndex(['salarie_id']);
            $table->dropIndex(['tresorerie_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('paie_elements', function (Blueprint $table) {
            $table->dropIndex(['paie_id']);
        });
    }
};
