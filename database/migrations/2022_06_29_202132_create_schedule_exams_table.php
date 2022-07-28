<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_exams', function (Blueprint $table) {
            $table->id();
            $table->string('topicname', 100);
            $table->date('examdate')->nullable();
            $table->jsonb('slotid')->nullable();
            $table->integer('totalexamparticipant')->nullable();
            $table->integer('coursemaster')->nullable();
            $table->jsonb('courseparallel')->nullable();
            $table->integer('coordinator')->nullable();
            $table->boolean('is_parallel')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->boolean('is_deployed')->nullable();
            $table->text('status')->nullable();
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
        Schema::dropIfExists('schedule_exams');
    }
}
