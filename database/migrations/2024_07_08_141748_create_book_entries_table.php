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
        Schema::create('book_entries', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('book_id')->index();
            $table->unsignedBigInteger('group_type_id')->nullable()->index();
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->boolean('is_reserved')->default(false);
            $table->unsignedBigInteger('reserved_by')->nullable()->index();
            $table->dateTime('reserved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable()->index();
            $table->dateTime('approved_at')->nullable();
            $table->unsignedBigInteger('disapproved_by')->nullable()->index();
            $table->dateTime('disapproved_at')->nullable();
            $table->boolean('is_sold')->default(false);
            $table->date('sold_date')->nullable();
            $table->unsignedBigInteger('rented_by')->nullable()->index();
            $table->unsignedBigInteger('purchased_by')->nullable()->index();
            $table->string('extra_request')->nullable();
            $table->unsignedBigInteger('requested_by')->nullable()->index();
            $table->string('state', 100)->nullable();
            $table->date('due_date')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->unsignedBigInteger('canceled_by')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->boolean('notified')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('image_at_reservering')->nullable();
            $table->string('image_at_returning')->nullable();
            $table->string('original_condition')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('group')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('reserved_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('disapproved_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('rented_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('purchased_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('canceled_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_entries');
    }
};
