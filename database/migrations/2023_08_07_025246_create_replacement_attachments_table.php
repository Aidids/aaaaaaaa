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
        Schema::create('replacement_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('redeem_replacement_leave_id')->unsigned()->nullable()->default(null);
            $table->string('name');
            $table->string('path')->nullable()->default(null);

            $table->foreign('redeem_replacement_leave_id')
                ->references('id')
                ->on('redeem_replacement_leaves')
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
        Schema::dropIfExists('replacement_attachments');
    }
};
