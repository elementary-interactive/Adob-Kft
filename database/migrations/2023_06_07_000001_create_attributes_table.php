<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributesTable extends Migration
{
  /** Create `attibutes` table.
   * 
   * @return void
   */
  public function up()
  {
    Schema::create('attributes', function (Blueprint $table) {
      $table->uuid('id'); // We are using UUID as primary key.

      $table->string('class');

      $table->string('name');
      $table->string('slug');

      $table->string('field')
        ->default('text');

      $table->string('cast_as')
        ->default('string');

      $table->string('rules')
        ->nullable()
        ->default(null);


      $table->timestamps();
      $table->softDeletes();

      $table->primary('id');
      $table->index('class');
      $table->index('deleted_at');
    });
  }

  /** Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('attributes');
  }
}
