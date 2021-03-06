<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->default('')->nullable();
            $table->string('phone')->unique()->default('')->nullable();
            $table->integer('department_id')->nullable();
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onUpdate('cascade');
            $table->string('password')->default('')->nullable();
            $table->boolean('admin')->default(false)->nullable();
            $table->boolean('disabled')->default(false)->nullable();
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

}
