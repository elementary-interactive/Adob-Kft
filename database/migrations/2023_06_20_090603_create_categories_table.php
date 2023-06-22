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
        Schema::create('categories', function (Blueprint $table) {
            // These columns are needed for Baum's Nested Set implementation to work.
          // Column names may be changed, but they *must* all exist and be modified
          // in the model.
          // Take a look at the model scaffold comments for details.
          // We add indexes on parent_id, lft, rgt columns by default.
          $table->uuid('id');
          $table->uuid('parent_id')
            ->nullable();
          
          $table->integer('lft')
            ->nullable()
            ->index();
          
          $table->integer('rgt')
            ->nullable()
            ->index();
          $table->integer('depth')
            ->nullable()
            ->index();

          // Add needed columns here (f.ex: name, slug, path, etc.)
          $table->string('name');
          $table->string('slug');
          $table->text('description')
            ->nullable(); //import
          $table->text('description_manual')
            ->nullable(); //manual
          $table->integer('products')
            ->nullable();
          $table->integer('active_products')
            ->nullable();

          $table->timestamps();
          $table->softDeletes();

          $table->primary('id');
          $table->foreign('parent_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
