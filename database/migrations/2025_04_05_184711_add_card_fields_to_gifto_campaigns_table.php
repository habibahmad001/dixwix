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
        Schema::table('gifto_campaigns', function (Blueprint $table) {
            $table->text('card_bg')->nullable()->after('compaign_status');
            $table->string('card_title', 256)->nullable()->after('card_bg');
            $table->text('card_message')->nullable()->after('card_title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gifto_campaigns', function (Blueprint $table) {
            $table->dropColumn(['card_bg', 'card_title', 'card_message']);
        });
    }
};
