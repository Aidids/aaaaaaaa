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
        Schema::create('transports', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('travel_id')->unsigned()->nullable()->default(null);
            $table->foreign('travel_id', 'travel_id')
                ->references('id')
                ->on('travel_claims')
                ->onDelete('set null');

            $table->string('transport_type')->nullable();
            $table->date('date')->nullable();
            $table->string('start_location')->nullable()->default(null);
            $table->string('end_location')->nullable()->default(null);
            $table->decimal('total_distance',13,2)->nullable()->default(null);
            $table->decimal('amount',13,2)->unsigned()->nullable()->default(null);
            $table->string('remark')->nullable()->default(null);
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
        Schema::dropIfExists('transports');
    }
};
