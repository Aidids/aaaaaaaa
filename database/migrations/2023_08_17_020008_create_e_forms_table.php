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
        Schema::create('e_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('eformable_id');
            $table->string('eformable_type');
            $table->integer('user_id')->unsigned()->nullable()->default(null);

            $table->integer('first_approver_id')->unsigned()->nullable()->default(null);
            $table->string('first_approver_status')->nullable()->default(null);
            $table->string('first_approver_remark')->nullable()->default(null);
            $table->date('first_approver_date')->nullable()->default(null);

            $table->integer('second_approver_id')->unsigned()->nullable()->default(null);
            $table->string('second_approver_status')->nullable()->default(null);
            $table->string('second_approver_remark')->nullable()->default(null);
            $table->date('second_approver_date')->nullable()->default(null);

            $table->integer('hr_ic_id')->unsigned()->nullable()->default(null);
            $table->string('hr_ic_status')->nullable()->default(null);
            $table->string('hr_ic_remark')->nullable()->default(null);
            $table->date('hr_ic_date')->nullable()->default(null);

            $table->string('overall_status')->nullable()->default(null);

            $table->foreign('user_id')
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
        Schema::dropIfExists('e_forms');
    }
};
