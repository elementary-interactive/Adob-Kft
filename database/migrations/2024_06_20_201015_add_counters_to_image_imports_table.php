<?php

use Neon\Models\Statuses\BasicStatus;
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
        Schema::table('image_imports', function (Blueprint $table) {
            $table->integer('records_counter', false, true)
                ->default(0)
                ->after('images');
            $table->integer('records_handled', false, true)
                ->default(0)
                ->after('images');
        });
    }

    public function down()
    {
        Schema::table('image_imports', function (Blueprint $table) {
            $table->dropColumn('records_counter');
            $table->dropColumn('records_handled');
        });
    }
};