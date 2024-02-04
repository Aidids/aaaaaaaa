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
        Schema::create('personal_information', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned()->nullable()->default(null);
            $table->string('nickname')->nullable()->default(null);
            $table->date('date_of_birth')->nullable()->default(null);
            $table->string('place_of_birth')->nullable()->default(null);

            $table->string('ic_no')->nullable()->default(null);
            $table->string('passport_no')->nullable()->default(null);
            $table->string('phone_no')->nullable()->default(null);

            $table->string('race')->nullable()->default(null);
            $table->string('religion')->nullable()->default(null);
            $table->string('nationality')->nullable()->default(null);

            /* 0 = single | 1 = married */
            $table->boolean('marital_status')->nullable()->default(0);
            $table->string('spouse_name')->nullable()->default(null);
            $table->string('spouse_ic_no')->nullable()->default(null);
            /* 0 = not working | 1 = working */
            $table->boolean('spouse_work')->nullable()->default(null);
            $table->string('marriage_cert_path')->nullable()->default(null);

            $table->string('epf_no')->nullable()->default(null);
            $table->string('socso_no')->nullable()->default(null);
            $table->string('income_tax_no')->nullable()->default(null);
            $table->string('bank_name')->nullable()->default(null);
            $table->string('bank_acc_type')->nullable()->default(null);
            $table->string('bank_acc_no')->nullable()->default(null);

            $table->json('educations')->nullable()->default(null);

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
        Schema::dropIfExists('personal_information');
    }
};
