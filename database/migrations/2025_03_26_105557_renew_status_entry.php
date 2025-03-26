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
        Schema::table('book_entries', function (Blueprint $table) {
            $table->integer('is_renew')->default(0)->after('is_reserved');
            $table->dateTime('renew_date')->nullable()->after('is_renew');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_entries', function (Blueprint $table) {
            $table->dropColumn('renew_date');
            $table->dropColumn('is_renew');
        });
    }
};
