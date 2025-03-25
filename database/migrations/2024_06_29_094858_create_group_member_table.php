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
            Schema::create('group_member', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id')->nullable()->index();
                $table->unsignedBigInteger('group_id')->nullable()->index();
                $table->enum('status', ['invited', 'requested', 'added'])->default('invited');
                $table->boolean('activated')->default(false);
                $table->enum('member_role', ['user', 'admin'])->default('user');
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->unsignedBigInteger('updated_by')->nullable()->index();
                $table->softDeletes();
                $table->timestamps();
                $table->unique(['member_id', 'group_id'], 'unique_groupmember');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_member');
    }
};
