<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');

            $table->unsignedBigInteger('routing_id')->nullable();
            $table->integer('placeNum')->nullable();
            $table->dateTime('dateTime')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->integer('daysNum')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->dateTime('eatDateTime')->nullable();
            $table->double('cost');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
            $table->foreign('routing_id')->references('id')->on('routings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
