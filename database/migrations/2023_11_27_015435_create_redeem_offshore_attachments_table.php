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
        Schema::create('redeem_offshore_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('redeem_offshore_leave_id')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->string('path')->nullable()->default(null);

            $table->foreign('redeem_offshore_leave_id')
                ->references('id')
                ->on('redeem_offshore_leaves')
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
        Schema::dropIfExists('redeem_offshore_attachments');
    }
};
