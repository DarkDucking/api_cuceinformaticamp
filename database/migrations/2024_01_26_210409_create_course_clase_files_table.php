<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseClaseFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_clase_files', function (Blueprint $table) {
            // ... otras columnas ...
            $table->softDeletes(); // Añadir softDeletes() si no está presente
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
   
}
