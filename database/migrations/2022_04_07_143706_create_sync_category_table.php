<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_category', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('category_name', 255)->nullable();
            $table->string('shortname', 10)->nullable();
            $table->string('category_type', 150)->nullable()->index();
            $table->string('initial_studyprogram', 10)->nullable();
            $table->integer('cateogry_parent_id')->nullable()->index();
            $table->string('group_leader', 150)->nullable();
            $table->timestamp('sync_date')->nullable();
            $table->string('sync_status', 80)->nullable();
            $table->string('flag_status', 80)->nullable();
            $table->string('table_owner', 80)->nullable();
            $table->integer('table_id')->nullable();
            $table->timestamp('updated_date')->nullable();
            $table->string('updated_id', 80)->nullable();
            $table->boolean('is_manual_insert')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_category');
    }
}
