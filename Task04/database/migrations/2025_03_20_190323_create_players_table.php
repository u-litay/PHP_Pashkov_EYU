<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->integer('number1');
            $table->integer('number2');
            $table->integer('gcd');
            $table->integer('player_answer')->nullable();
            $table->string('result')->nullable();
            $table->timestamp('played_at')->nullable(); // Убрали useCurrent()
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('players');
    }
};
