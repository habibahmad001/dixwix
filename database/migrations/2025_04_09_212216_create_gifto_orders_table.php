<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gifto_orders', function (Blueprint $table) {
            $table->id();
            $table->string('userEmail', 256)->nullable();
            $table->string('userName', 256)->nullable();
            $table->string('points', 256)->nullable();
            $table->string('giftoAmount', 256)->nullable();
            $table->string('giftoMsg', 256)->nullable();
            $table->string('campaignUuid', 256)->nullable();
            $table->text('selectedCard')->nullable();
            $table->text('cardPath')->nullable();
            $table->enum('orderStatus', ['active', 'inactive', 'processed', 'completed'])->default('inactive');
            $table->enum('status', ['yes', 'no'])->default('yes');
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('created_by', 256)->default('user');
            $table->string('updated_by', 256)->default('user');
            $table->softDeletes(); // <- Soft Delete column (deleted_at)
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('id'); // explicit index (even though primary key already has one)
            $table->index('user_id');
            $table->index('campaignUuid');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifto_orders');
    }
};
