<?php

use App\Enums\LeaveRequestDateType;
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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('leave_balance_id')->unsigned()->nullable()->default(null);
            $table->date('start_date');
            $table->string('start_date_type')->default(LeaveRequestDateType::fullDay->value);
            $table->date('end_date');
            $table->string('end_date_type')->default(LeaveRequestDateType::fullDay->value);
            $table->double('duration');
            /* 0 = first half | 1 = second half */
            $table->boolean('leave_type')->nullable();
            $table->string('reason')->nullable();

            $table->integer('first_approver_id')->unsigned()->nullable()->default(null);
            $table->string('first_approver_status')->nullable()->default(null);
            $table->string('first_approver_remark')->nullable()->default(null);
            $table->date('first_approver_date')->nullable()->default(null);
            $table->integer('second_approver_id')->unsigned()->nullable()->default(null);
            $table->string('second_approver_status')->nullable()->default(null);
            $table->string('second_approver_remark')->nullable()->default(null);
            $table->date('second_approver_date')->nullable()->default(null);

            $table->string('overall_status')->default(LeaveRequestStatus::pending->value);
            $table->boolean('calculated')->nullable()->default(null);

            $table->foreign('user_id')
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
        Schema::dropIfExists('leave_requests');
    }
};
