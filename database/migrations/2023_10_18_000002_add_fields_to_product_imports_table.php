<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_imports', function (Blueprint $table) {
            $table->uuid('batch_id')
                ->nullable()
                ->default(null)
                ->after('id');
            $table->integer('records_counter', false, true)
                ->default(0)
                ->after('imported_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_imports', function (Blueprint $table) {
            $table->dropColumn('batch_id');
            $table->dropColumn('records_counter');
        });
    }
};
