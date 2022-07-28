<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncLmsCourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_lms_course', function (Blueprint $table) {
            $table->id('course_id');

            $table->integer('category_id')->nullable();
            $table->string('semester', 6)->nullable();
            $table->string('class', 50)->nullable();
            $table->string('subject_code', 50)->nullable();
            $table->string('subject_name', 255)->nullable();
            $table->string('sync_status', 100)->nullable();
            $table->timestamp('sync_date')->nullable();
            $table->string('flag_status', 50)->nullable();

            $table->string('table_owner', 50)->nullable();
            $table->integer('table_id')->nullable();
            $table->integer('subject_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('user_username', 40)->nullable();
            $table->string('employeeid', 20)->nullable();
            $table->string('lecturercode', 3)->nullable();
            $table->string('employee_name', 150)->nullable();

            $table->timestamp('last_sync')->nullable();
            $table->boolean('is_synced')->nullable();
            $table->boolean('is_deleted')->nullable()->default(0);
            $table->integer('backup_state')->default(0);
            $table->string('backup_path', 255)->nullable();
            $table->timestamp('last_backup')->nullable();
            $table->string('backup_filename', 255)->nullable();
            $table->integer('delete_state')->default(0);

            $table->timestamp('last_delete ')->nullable();
            $table->timestamp('course_completion_updated')->nullable();
            $table->integer('last_completion_attempt')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_lms_course');
    }
}
