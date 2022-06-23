<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('plantilla_id');
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->boolean('notificar_email');
            $table->boolean('notificar_sms');
            $table->dateTime('last_activity')->nullable();
            $table->dateTime('next_activity')->nullable();
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
        Schema::dropIfExists('notificaciones');
    }
}
