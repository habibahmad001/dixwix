<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileHashToImportedfilesTable extends Migration
{
    public function up()
    {
        Schema::table('importedfile', function (Blueprint $table) {
            $table->string('file_hash')->nullable()->after('path'); // Add the file_hash column
        });
    }

    public function down()
    {
        Schema::table('importedfile', function (Blueprint $table) {
            $table->dropColumn('file_hash'); // Remove the file_hash column
        });
    }
}
