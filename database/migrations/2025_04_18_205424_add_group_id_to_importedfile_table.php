<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('importedfile', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id')->nullable()->after('file_hash');
        });
    }

    public function down()
    {
        Schema::table('importedfile', function (Blueprint $table) {
            $table->dropColumn('group_id');
        });
    }
};
