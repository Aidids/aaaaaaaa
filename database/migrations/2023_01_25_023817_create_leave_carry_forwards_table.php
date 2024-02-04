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
        Schema::create('leave_carry_forwards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leave_type_id')->unsigned()->nullable()->default(null);
            $table->integer('start_period');
            $table->integer('end_period');
            $table->integer('amount');

            $table->foreign('leave_type_id')
                ->references('id')
                ->on('leave_types')
                ->onDelete('set null');

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
        Schema::dropIfExists('leave_carry_forwards');
    }
};
