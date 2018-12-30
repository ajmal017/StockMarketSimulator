<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHaveShares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('have_shares', function (Blueprint $table) {
            $table->bigIncrements('rid');
            $table->integer('iid');
            $table->integer('sid');
            $table->integer('amount');
            $table->double('buying_price', 15, 2);
            $table->double('selling_price', 15, 2);

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
        Schema::dropIfExists('have_shares');
    }
}
