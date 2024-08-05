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
        Schema::create('transportations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transporter_id');
            $table->integer('totalCapacity');
            $table->double('cost');
            $table->set('type', ['normalPlane', 'businessClassPlane', 'pullman', 'bus', 'van']);
            $table->timestamps();

            $table->foreign('transporter_id')->references('id')->on('transporters')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportations');
    }
};
