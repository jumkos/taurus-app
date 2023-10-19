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
        Schema::table('user_details', function (Blueprint $table) {
            $table->double('rating')->nullable();
            $table->integer('rating_by')->nullable();
            $table->double('point')->nullable();
            $table->renameColumn('branch_location_id', 'city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->removeColumn('rating');
            $table->removeColumn('rating_by');
            $table->removeColumn('point');
        });
    }
};
