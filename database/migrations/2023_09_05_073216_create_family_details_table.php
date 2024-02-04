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
        Schema::create('family_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->integer('personal_information_id')->unsigned()->nullable()->default(null);

            $table->string('child_name')->nullable()->default(null);
            $table->string('child_ic_no')->nullable()->default(null);
            $table->string('child_cert_path')->nullable()->default(null);

            $table->foreign('personal_information_id')
                ->references('id')
                ->on('personal_information')
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
        Schema::dropIfExists('family_details');
    }
};
