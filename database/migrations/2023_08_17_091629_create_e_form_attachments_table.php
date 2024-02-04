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
        Schema::create('e_form_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('e_form_id')->unsigned()->nullable()->default(null);
            $table->string('name')->nullable()->default(null);
            $table->string('path')->nullable()->default(null);
            $table->boolean('hr_upload')->nullable()->default(0);

            $table->foreign('e_form_id')
                ->references('id')
                ->on('e_forms')
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
        Schema::dropIfExists('e_form_attachments');
    }
};
