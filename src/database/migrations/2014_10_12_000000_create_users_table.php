<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('nombre');
            $table->unsignedInteger('cedula');
            $table->string('email1');
            $table->string('email2');
            $table->string('direccion');
            $table->unsignedInteger('empresa_id')->nullable();
            //$table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->unsignedInteger('rol_id');
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
};
