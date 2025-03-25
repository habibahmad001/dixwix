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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('biodata')->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->longText('locations')->nullable();
            $table->string('stripe_customer_id')->nullable();
            $table->string('profile_pic')->nullable();
            $table->integer('group_type')->nullable();
            $table->text('source')->nullable();
            $table->text('external_id')->nullable();
            $table->text('address')->nullable();
            $table->string('state',35)->nullable();
            $table->string('zipcode',20)->nullable();
            $table->longText('group_locations')->nullable();
            $table->text('activation_code')->nullable();
            $table->text('web_fcm_token')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
