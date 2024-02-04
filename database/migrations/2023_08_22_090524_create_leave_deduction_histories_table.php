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
        Schema::create('leave_deduction_histories', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->string('remark')->nullable();
            $table->double('duration');
            $table->integer('hr_ic_id')->unsigned()->nullable()->default(null);
            $table->date('hr_ic_date');
            $table->boolean('deduct_all')->nullable()->default(null);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('hr_ic_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('leave_deduction_histories');
    }
};
