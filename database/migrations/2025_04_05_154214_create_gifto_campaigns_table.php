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
        Schema::create('gifto_campaigns', function (Blueprint $table) {
            $table->id(); // id (int 100, auto)
            $table->string('group_id', 256)->nullable();
//            $table->json('group_id')->nullable();
            $table->string('compaign_uuid', 256)->nullable();
            $table->string('compaign_name', 256)->nullable();
            $table->string('compaign_denominations', 256)->nullable();
            $table->string('compaign_status', 256)->nullable();
            $table->enum('status', ['enabled', 'disabled'])->default('disabled');
            $table->string('created_by', 256)->default('admin');
            $table->string('updated_by', 256)->default('admin');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifto_campaigns');
    }
};
