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
        Schema::create('leave_request_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('leave_request_id')->unsigned()->nullable()->default(null);
            $table->string('path')->nullable()->default(null);

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
        Schema::dropIfExists('leave_request_attachments');
    }
};
