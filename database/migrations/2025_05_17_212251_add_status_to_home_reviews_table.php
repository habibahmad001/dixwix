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
        Schema::table('home_reviews', function (Blueprint $table) {
            $table->enum('status', ['approved', 'unapproved'])
                ->nullable()
                ->default('unapproved')
                ->after('textDescription'); // Optional: position in table
        });
    }

    public function down()
    {
        Schema::table('home_reviews', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

};
