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
        Schema::create('users', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('department_id')->unsigned()->nullable()->default(null);
            $table->integer('approver_id')->unsigned()->nullable()->default(null);

            $table->string('name');
            $table->boolean('is_admin')->default(false);
            $table->string('username')->unique();
            $table->string('password');

            $table->string('dn')->nullable()->default(null);
            $table->string('title')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->char('gender')->nullable()->default(null);
            $table->string('contact_no')->nullable()->default(null);
            $table->date('date_of_birth')->nullable()->default(null);
            $table->date('joining_date')->nullable()->default(null);

            $table->rememberToken();

            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('set null');

            $table->foreign('approver_id')
                ->references('id')
                ->on('approvers')
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
        Schema::dropIfExists('users');
    }
};
