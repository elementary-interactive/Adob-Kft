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
        Schema::create('brands', function (Blueprint $table) {
        
            $table->uuid('id');
            
            $table->string('name');                         /** Márkanév neve */
            $table->string('slug');                         /** Márkanév URL-je */
            $table->boolean('is_featured')
                ->default(false);                           /** Márka kiemelése */
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->primary('id');
            $table->fullText('name');
            $table->unique('slug');
            $table->index('is_featured');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
