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
        Schema::create('image_imports', function (Blueprint $table) {
            $table->uuid('id')
                ->primary();
            $table->uuid('imported_by_id')
                ->nullable()
                ->default(null);
            $table->string('status')
                ->nullable()
                ->default(null);
            $table->longText('images')
                ->nullable()
                ->default(null);

            $table->longText('data')
                ->nullable()
                ->default(null);
            $table->text('job')
                ->nullable()
                ->default(null);
            $table->string('file')
                ->nullable()
                ->default(null);

            $table->timestamp('finished_at')
                ->nullable()
                ->default(null);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('imported_by_id')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_imports');
    }
};
