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
        Schema::create('item_rejected_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entry_id')->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('book_id')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('disapproved_by')->nullable();
            $table->timestamp('disapproved_at');
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->foreign('entry_id')->references('id')->on('book_entries')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('book')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_rejected_requests');
    }
};
