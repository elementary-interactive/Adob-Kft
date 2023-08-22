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
        Schema::create('product_imports', function (Blueprint $table) {
            $table->uuid('id')
                ->primary();
            $table->uuid('imported_by');

            $table->integer('products_inserted', false, true);
            $table->integer('products_modified', false, true);
            $table->integer('brands_inserted', false, true);
            $table->integer('brands_modified', false, true);
            $table->integer('categories', false, true);

            $table->text('data');

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('imported_by')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
