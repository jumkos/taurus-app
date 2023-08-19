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
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('issuer_id');
            $table->bigInteger('refer_id');
            $table->string('cust_name');
            $table->string('phone');
            $table->string('address');
            $table->dateTime('offering_date');
            $table->bigInteger('product_type_id');
            $table->bigInteger('product_category_id');
            $table->bigInteger('product_id');
            $table->decimal('nominal', 15, 2);
            $table->string('info');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
