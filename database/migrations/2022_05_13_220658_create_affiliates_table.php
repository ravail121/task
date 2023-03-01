<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('merchant_id');
            // TODO: Replace me with a brief explanation of why floats aren't the correct data type, and replace with the correct data type.
            // Float is a data type that can have wide ranges of values e.g 9.999999999 to very large 99999999.9 while commission rates looks 
            // as a commission percentage rate  which wont be very long in decimals. So i will use decimal for that.
            $table->decimal('commission_rate', 10, 2);

            // And if its just percentage than i can store it as an integer too.
            // $table->integer('commission_rate')->unsigned();
            $table->string('discount_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliates');
    }
};
