<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttributeValuesTable extends Migration
{
  /** Create `contents` table.
   * 
   * @return void
   */
  public function up()
  {
    Schema::create('attribute_values', function (Blueprint $table) {
      $table->uuid('id'); // We are using UUID as primary key.
      
      $table->uuid('attribute_id'); // We are using UUID as primary key.
      
      $table->text('value');

      $table->string('attributable_type', 255)
        ->nullable()
        ->default(null);
      $table->uuid('attributable_id')
        ->nullable()
        ->default(null);

      $table->timestamp('published_at')
        ->default(null);
      $table->timestamp('expired_at')
        ->nullable()
        ->default(null);
      $table->timestamps();
      $table->softDeletes();
      
      $table->primary('id');
      $table->foreign('attribute_id')
        ->references('id')->on('attributes')
        ->onDelete('cascade');
      $table->index([
        'attributable_type',
        'attributable_id'
      ]);
      $table->index('deleted_at');
    });
  }

  /** Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('attribute_values');
  }
}