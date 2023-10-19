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
            $table->integer('fails_counter', false, true)
                ->default(0)
                ->after('categories_modified');
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
            $table->dropColumn('fails_counter');
        });
    }
};
