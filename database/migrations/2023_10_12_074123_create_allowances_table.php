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
        Schema::create('allowances', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('travel_id')->unsigned()->nullable()->default(null);
            $table->foreign('travel_id')
                ->references('id')
                ->on('travel_claims')
                ->onDelete('set null');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('allowance_type')->nullable();
            $table->decimal('allowance_rate',13,2)->nullable();
            $table->decimal('meal_total_hours',13,1)->nullable();
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
        Schema::dropIfExists('allowances');
    }
};
