<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParamsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('params', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name', 100);
			$table->enum('type', ['outsource-position', 'staff-position', 'religion', 'tribe', 'location', 'disease', 'formal-education', 'training', 'language', 'district', 'status-of-residence']);
			
			$table->integer('created_by');
			$table->integer('updated_by');
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
		Schema::drop('params');
	}
}
