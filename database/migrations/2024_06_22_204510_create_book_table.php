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
        Schema::create('book', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->unique()->nullable();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('group_type_id')->nullable()->index();
            $table->unsignedBigInteger('group_id')->nullable()->index();
            $table->unsignedBigInteger('type_id')->nullable()->index();
            $table->text('writers')->nullable();
            $table->string('cover_page')->nullable();
            $table->string('latest_image')->nullable();
            $table->integer('year')->nullable();
            $table->integer('pages')->nullable();
            $table->longText('journal_name')->nullable();
            $table->string('ean_isbn_no', 50)->nullable();
            $table->string('upc_isbn_no', 50)->nullable();
            $table->date('added_date')->nullable();
            $table->integer('copies')->nullable();
            $table->string('ref_type', 100)->nullable();
            $table->text('ref_link')->nullable();
            $table->string('barcode', 120)->nullable();
            $table->string('barcode_url')->nullable();
            $table->enum('sale_or_rent', ['sale','rent']);
            $table->decimal('price', 8, 2)->default(0.00);
            $table->decimal('rent_price', 8, 2)->default(0.00);
            $table->boolean('featured')->default(false);
            $table->boolean('status')->default(false);
            $table->boolean('in_maintenance')->default(false);
            $table->longText('locations')->nullable();
            $table->text('keyword')->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->string('status_options', 100)->nullable();
            $table->string('condition', 100)->nullable();
            $table->string('weight', 100)->nullable();
            $table->string('weightKgLbs', 100)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book');
    }
};
