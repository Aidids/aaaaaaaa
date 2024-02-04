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
        Schema::create('portal_notifications', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->string('color')->nullable()->default(null);
            $table->string('model_name')->nullable()->default(null);
            $table->integer('model_id')->unsigned()->nullable()->default(null);
            $table->string('requester_name')->nullable();
            $table->string('status')->nullable()->default(LeaveRequestStatus::pending->value);
            $table->date('read_at')->nullable()->default(null);

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
        Schema::dropIfExists('portal_notifications');
    }
};
