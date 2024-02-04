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
        Schema::create('travel_authorizations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('department_id')->unsigned()->nullable()->default(null);
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');

            $table->boolean('travel_purpose')->default(false);
            $table->string('project_name')->nullable();
            $table->string('project_location')->nullable();
            $table->integer('main_office');
            $table->integer('reimbursement');
            $table->json('location');
            $table->string('purpose')->nullable();
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
        Schema::dropIfExists('travel_expenses');
    }
};
