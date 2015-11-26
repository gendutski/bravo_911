<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_menu', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('menu_id');
            $table->boolean('method_post');
            $table->boolean('method_put');
            $table->boolean('method_delete');
            
            $table->primary(['user_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_menu');
    }
}
