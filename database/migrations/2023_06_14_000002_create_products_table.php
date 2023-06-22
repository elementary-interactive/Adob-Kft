<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Neon\Models\Statuses\BasicStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
        
            $table->uuid('id');
            $table->uuid('parent_id')
                ->nullable();
            $table->uuid('brand_id');
            $table->bigInteger('media_id')
                ->unsigned()
                ->nullable()
                ->default(null);
            $table->string('product_id');                   /** Cikkszám */
            $table->string('product_number')                /**  */
                ->nullable()
                ->default(null);
            $table->string('name');                         /** Termék neve */
            $table->string('slug');                         /** Termék URL-je */
            $table->string('packaging', 25)                 /** Csomagolás */
                ->nullable();
            $table->text('description')                     /** Termék leírása */
                ->nullable();
            $table->string('ean', 13)                       /** EAN kód */
                ->nullable();
            $table->decimal('price', 18, 2)                 /** Termék ára */
                ->nullable();
            $table->boolean('on_sale')                      /** Akciós-e */
                ->default(false);
            $table->char('status', 1)
                ->default(BasicStatus::default()->value);
            $table->json('og_data')
                ->nullable()
                ->default(null);
            $table->json('meta_data')
                ->nullable()
                ->default(null);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->primary('id');
            $table->foreign('parent_id')->references('id')->on('products');
            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('media_id')->references('id')->on('media');
            $table->fullText([
                'name',
                'product_id',
                'description',
                'product_number',
                'ean',
                'packaging'
            ], 'adob_fulltext');
            $table->unique('product_id');
            $table->unique('slug');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
