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
        Schema::create('travel_claims', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->boolean('custom_approver')->nullable()->default(false);
            $table->integer('department_id')->unsigned()->nullable()->default(null);
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');

            $table->integer('current_approver')->unsigned()->nullable()->default(null);
            $table->json('approvers_id')->nullable()->default(null);
            $table->json('approvers_remark')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->date('submission_month')->nullable()->default(null);
            $table->decimal('total_allowance',13,2)->nullable()->default(null);
            $table->decimal('total_transport',13,2)->nullable()->default(null);
            $table->decimal('total_expense',13,2)->nullable()->default(null);
            $table->boolean('isDraft')->default(true);
            $table->integer('index_page')->nullable()->default(0);
            $table->string('hr_note')->nullable()->default(null);

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
        Schema::dropIfExists('travel_claims');
    }
};
