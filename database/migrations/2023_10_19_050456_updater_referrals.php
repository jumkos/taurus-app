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
        Schema::table('referrals', function (Blueprint $table) {
            $table->integer('refer_to_division');
            $table->integer('refer_to_region');
            $table->integer('refer_to_city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->removeColumn('refer_to_division');
            $table->removeColumn('refer_to_region');
            $table->removeColumn('refer_to_city');
        });
    }
};
