<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalFamilyTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('personal_families', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('personal_id')->unsigned();
			$table->enum('tipe', ['suami-istri', 'anak', 'ayah', 'ibu', 'adik-kakak']);
			$table->string('nama', 255);
			$table->string('tempat_lahir', 255)->nullable();
			$table->date('tgl_lahir')->nullable();
			$table->text('alamat')->nullable();
			$table->string('pekerjaan', 255)->nullable();
			
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
		Schema::drop('personal_families');
	}
}
