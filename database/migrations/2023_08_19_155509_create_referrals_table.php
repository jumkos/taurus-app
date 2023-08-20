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
            $table->string('cust_name', 250);
            $table->string('phone', 15);
            $table->string('address', 1000);
            $table->dateTime('offering_date');
            $table->bigInteger('product_type_id');
            $table->bigInteger('product_category_id');
            $table->bigInteger('product_id');
            $table->string('product_detail', 250);
            $table->bigInteger('nominal');
            $table->string('info', 1000);
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
