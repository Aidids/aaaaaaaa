<?php

use App\Enums\LeaveRequestStatus;
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
        Schema::create('redeem_replacement_leaves', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('leave_request_id')->unsigned()->nullable()->default(null);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('remark')->nullable()->default(null);

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
            $table->double('added_qty')->nullable()->default(null);
            $table->double('balance_qty')->nullable()->default(null);

            $table->string('overall_status')->default(LeaveRequestStatus::pending->value);
            $table->timestamp('expired_date')->nullable()->default(null);


            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->foreign('leave_request_id')
                ->references('id')
                ->on('leave_requests')
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
        Schema::dropIfExists('redeem_replacement_leaves');
    }
};
