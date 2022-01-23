<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->foreignId('user_id')->nullable();
            $table->string('title');
            $table->double('price', 15, 8);
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('product_id');
            $table->index('user_id');
            $table->index('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            Schema::dropIfExists('products');
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        });
    }
}
