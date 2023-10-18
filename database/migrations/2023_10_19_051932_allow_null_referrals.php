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
            $table->integer('refer_id')->nullable()->change();
            $table->integer('issuer_rating')->nullable()->change();
            $table->string('issuer_comment')->nullable()->change();
            $table->integer('refer_rating')->nullable()->change();
            $table->string('refer_comment')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->integer('refer_id')->change();
            $table->integer('issuer_rating')->change();
            $table->string('issuer_comment')->change();
            $table->integer('refer_rating')->change();
            $table->string('refer_comment')->change();
        });
    }
};
