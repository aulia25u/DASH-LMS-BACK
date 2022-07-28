<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamScheduleSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_exam_slots', function (Blueprint $table) {
            $table->id();
            $table->string('slotname', 50);
            $table->time('starttime', $precision = 0)->nullable();
            $table->time('endtime', $precision = 0)->nullable();
            $table->boolean('is_active')->nullable();
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
        Schema::dropIfExists('exam_schedule_slots');
    }
}
