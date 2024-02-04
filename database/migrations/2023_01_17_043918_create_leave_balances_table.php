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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('leave_type_id')->unsigned()->nullable()->default(null);

            $table->double('proRated')->nullable();
            $table->double('entitlement')->nullable();
            $table->double('carry_forward')->nullable();

            $table->double('balance');
            $table->double('total');
            $table->double('taken')->default(0);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

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
        Schema::dropIfExists('leave_balances');
    }
};
