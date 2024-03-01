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
        $table->id();
        $table->string('name', 250);
        $table->string('surname', 250)->nullable();
        $table->string('email', 255)->unique();
        $table->string('avatar', 250)->nullable();
        $table->unsignedBigInteger('role_id')->nullable();
        $table->foreign('role_id')->references('id')->on('roles');
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->tinyInteger('state')->unsigned()->default(1);
        $table->tinyInteger('type_user')->unsigned()->default(1);

        // Agregar la columna 'profesion'
        $table->string('profesion', 250)->nullable()->default(null);

        // Agregar la columna 'description'
        $table->text('description')->nullable()->default(null);

        // Agregar la columna 'is_instructor'
        $table->tinyInteger('is_instructor')->nullable()->default(null)->comment('si es nulo no es instructor y si es 1, es instructor');

        $table->rememberToken();
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
