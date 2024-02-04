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
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('travel_id')->unsigned()->nullable()->default(null);
            $table->foreign('travel_id')
                ->references('id')
                ->on('travel_claims')
                ->onDelete('set null');

            $table->string('description')->nullable();
            $table->string('account_code')->nullable();
            $table->integer('total_hours')->nullable();
            $table->decimal('amount',13,2)->nullable();

            $table->string('remark')->nullable();
            $table->string('path')->nullable();

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
        Schema::dropIfExists('expenses');
    }
};
