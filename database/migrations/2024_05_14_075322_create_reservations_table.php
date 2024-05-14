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

            $table->dateTime('dateTime')->nullable();
            $table->unsignedBigInteger('transportation_id')->nullable();
            $table->integer('placeNum')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->integer('daysNum')->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->dateTime('eatDateTime')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('tables')->onDelete('cascade');
            $table->foreign('transportation_id')->references('id')->on('transportations')->onDelete('cascade');

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
