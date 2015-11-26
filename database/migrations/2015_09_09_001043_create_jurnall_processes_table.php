<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJurnallProcessesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jurnall_processes', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('account_code_id');
			$table->string('rekening', 30);
			$table->string('uraian', 255);
			$table->double('debet');
			$table->double('kredit');
			$table->date('tanggal');
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
		Schema::drop('jurnall_processes');
	}
}
