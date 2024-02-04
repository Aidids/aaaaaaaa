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
        Schema::create('leave_add_ons', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('pic_id')->unsigned()->nullable()->default(null);
            $table->integer('leave_balance_id')->unsigned()->nullable()->default(null);

            $table->double('new_balance');
            $table->double('added_qty');
            $table->string('remark')->nullable()->default(null);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('pic_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('leave_balance_id')
                ->references('id')
                ->on('leave_balances')
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
        Schema::dropIfExists('leave_add_ons');
    }
};
