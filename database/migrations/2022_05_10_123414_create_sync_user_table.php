<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_user', function (Blueprint $table) {
            $table->id('userid');
            $table->string('name', 150)->nullable();
            $table->string('nim_nip', 30)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('jenis', 30)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_user');
    }
}
