<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_accesses', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('leader');
            $table->text('function');
            $table->text('unit');
            $table->text('address');
            $table->text('cabinet');
            $table->text('phone');
            $table->date('perStart');
            $table->date('perEnd');
            $table->boolean('isLogin');
            $table->boolean('is1CUPP');
            $table->boolean('is1CZPP');
            $table->boolean('isAsuse');
            $table->boolean('isOmnius');
            $table->boolean('isUSB');
            $table->boolean('isFolderObmen');
            $table->boolean('isWorkFromUTD');
            $table->boolean('isEmail');
            $table->boolean('isInternet');
            $table->boolean('isConsult');
            $table->text('lanResource')->nullable();
            $table->text('otherProgram')->nullable();
            $table->text('pdf_path');
            $table->text('pdf_url');
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
        Schema::dropIfExists('resource_accesses');
    }
}
