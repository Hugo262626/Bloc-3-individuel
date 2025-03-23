<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    //Création de la table affichant les données du msg
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('receiver_id')->constrained('users');
            $table->text('message');
            $table->timestamp('date')->useCurrent();
            $table->timestamps();
        });
    }

    //Suppression de la table pour en créer une nvl pour le prochain message
    public function down()
    {
        Schema::dropIfExists('messages');
    }

};
