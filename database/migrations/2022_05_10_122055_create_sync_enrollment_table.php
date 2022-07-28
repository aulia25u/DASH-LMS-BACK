<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncEnrollmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_enrollment', function (Blueprint $table) {
            $table->id('enrollment_id');
            $table->integer('course_id')->nullable();
            $table->string('enrollment_type', 50)->nullable();
            $table->string('class', 50)->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_username', 100)->nullable();
            $table->string('user_email', 100)->nullable();
            $table->string('enrollment_status', 100)->nullable();
            $table->string('sync_status', 30)->nullable();
            $table->timestamp('sync_date')->nullable();
            $table->string('flag_status', 50)->nullable();
            $table->string('table_owner', 100)->nullable();
            $table->integer('table_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_enrollment');
    }
}
