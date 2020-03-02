<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('items')){
            Schema::create('items', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->decimal('value', 10, 2)->default(0)->nullable();
                $table->string('type')->nullable();
                $table->text('description')->nullable();

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
