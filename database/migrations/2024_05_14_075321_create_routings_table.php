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
        Schema::create('routings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transportation_id');
            $table->unsignedBigInteger('strLocation');
            $table->unsignedBigInteger('touristArea_id');
            $table->dateTime('dateTime');
            $table->double('cost');

            $table->timestamps();

            $table->foreign('transportation_id')->references('id')->on('transportations')->onDelete('cascade');
            $table->foreign('strLocation')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('touristArea_id')->references('id')->on('tourist_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routings');
    }
};
