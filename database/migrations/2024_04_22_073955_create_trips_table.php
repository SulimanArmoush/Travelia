<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->double('cost');
            $table->date('strDate');
            $table->date('endDate');
            $table->integer('totalCapacity');
            $table->string('img');
            $table->unsignedBigInteger('strLocation');
            $table->unsignedBigInteger('touristArea_id');
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('transporter_id')->nullable();
            $table->integer('capacity')->default(0);

            $table->timestamps();

            $table->foreign('organizer_id')->references('id')->on('organizers')->onDelete('cascade');
            $table->foreign('strLocation')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('touristArea_id')->references('id')->on('tourist_areas')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
            $table->foreign('transporter_id')->references('id')->on('transporters')->onDelete('cascade');

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
