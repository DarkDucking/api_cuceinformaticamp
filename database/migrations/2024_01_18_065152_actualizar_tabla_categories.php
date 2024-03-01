<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ActualizarTablaCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Agrega la nueva columna 'state'
            $table->TinyInteger('state')->unsigned()->default(1)->after('categorie_id');

            // Modifica la columna 'state' según tus requerimientos
            $table->T('state')->unsigned()->default(1)
                  ->comment('1 es activo y 2 es no activo')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Revierte los cambios realizados en el método up
            $table->dropColumn('state');
            $table->T('state')->unsigned()->default(1)->change();
        });
    }
}
