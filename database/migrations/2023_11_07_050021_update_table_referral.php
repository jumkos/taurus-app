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
            $table->string('address')->nullable()->change();
            $table->dateTime('offering_date')->nullable()->change();
            $table->integer('product_type_id')->nullable()->change();
            $table->integer('product_id')->nullable()->change();
            $table->string('product_detail')->nullable()->change();
            $table->string('contact_person')->nullable()->change();
            $table->string('relation')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            //
        });
    }
};
