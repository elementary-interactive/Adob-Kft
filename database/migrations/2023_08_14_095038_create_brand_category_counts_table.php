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
        Schema::create('brand_category_counts', function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('category_id');
            $table->uuid('brand_id');
            $table->smallInteger('counts', false, true);
            $table->timestamps();

            $table->primary('id');
            $table->unique(['brand_id', 'category_id']);
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
            $table->foreign('brand_id')
                ->references('id')
                ->on('brands');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_category_counts');
    }
};
