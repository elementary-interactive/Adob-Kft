<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->uuid('id')
                ->primary();
            $table->string('title');
            $table->string('slug');
            $table->string('favicon')
                ->nullable()
                ->default(null);
            $table->json('domains');
            $table->char('locale', 2)
                ->index('ni_locale');
            $table->text('robots')
                ->nullable()
                ->default(null);
            $table->boolean('default')
                ->default(false);

            $table->softDeletes($column = 'deleted_at', $precision = 0)
                ->index('ni_deleted_at');

            $table->unique(['slug', 'locale']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
}