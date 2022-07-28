<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_course', function (Blueprint $table) {
            $table->id('subject_id');

            $table->string('subject_code', 255)->nullable();
            $table->string('subject_name', 255)->nullable();
            $table->string('subject_type', 255)->nullable();
            $table->string('subject_ppdu', 50)->nullable();
            $table->string('credit', 50)->nullable();
            $table->string('curriculum_year', 4)->nullable();

            $table->integer('sync_by')->nullable();
            $table->string('sync_status', 50)->nullable();
            $table->timestamp('sync_date')->nullable();
            $table->string('flag_status', 50)->nullable();
            $table->string('table_owner', 50)->nullable();
            $table->integer('table_id')->nullable();

            $table->integer('category_id')->nullable();
            $table->integer('studyprogramid')->nullable();
            $table->integer('approve_status')->nullable();
            $table->timestamp('approve_date')->nullable();
            $table->text('notes')->nullable();
            $table->integer('approve_by')->nullable();

            $table->integer('input_by')->nullable();
            $table->timestamp('input_date')->nullable();
            $table->timestamp('last_backup')->nullable();
            $table->boolean('is_backup')->nullable();
            $table->boolean('is_manual_insert')->default(0);
            $table->boolean('is_deleted')->default(0);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_course');
    }
}
