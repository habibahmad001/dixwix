<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('review')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->timestamps();
            $table->foreign('item_id')->references('id')->on('book')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
