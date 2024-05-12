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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->double('cost');
            $table->dateTime('dateTime');
            $table->integer('totalCapacity');
            $table->string('imgs');
            $table->unsignedBigInteger('strLocation');
            $table->unsignedBigInteger('touristArea');
            $table->unsignedBigInteger('hotel');
            $table->unsignedBigInteger('restaurant');
            $table->unsignedBigInteger('transporter');
            $table->set('status', ['available','ongoing','finished'])->default('available');

            $table->timestamps();


            $table->foreign('organizer_id')->references('id')->on('organizers')->onDelete('cascade');
            $table->foreign('strLocation')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('touristArea')->references('id')->on('tourist_areas')->onDelete('cascade');
            $table->foreign('hotel')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('restaurant')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('transporter')->references('id')->on('transporters')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
