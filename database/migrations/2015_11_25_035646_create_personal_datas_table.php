<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonalDatasTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('personal_datas', function (Blueprint $table) {
			$table->increments('id');
			$table->string('nama_lengkap', 255);
			$table->integer('posisi')->unsigned();
			$table->integer('asal_ktp')->unsigned();
			$table->string('no_ktp', 100);
			$table->text('alamat_ktp');
			$table->date('masa_berlaku_ktp');
			$table->string('no_jamsostek', 100)->nullable();
			$table->string('no_npwp', 100)->nullable();
			$table->string('no_id_kta_security', 100)->nullable();
			$table->string('no_reg_kta_security', 100)->nullable();
			$table->integer('suku_bangsa')->unsigned();
			$table->string('email', 255);
			$table->boolean('status_menikah');
			$table->date('tgl_lahir');
			$table->integer('tempat_lahir');
			$table->enum('jenis_kelamin', ['pria', 'wanita']);
			$table->integer('agama')->unsigned();
			$table->integer('tinggi_badan');
			$table->integer('berat_badan');
			$table->text('alamat_tinggal_sekarang')->nullable();
			$table->integer('kabupaten')->unsigned();
			$table->integer('status_tempat_tinggal')->unsigned();
			$table->string('no_call_rumah', 30)->nullable();
			$table->string('no_contact_person', 30)->nullable();
			$table->string('no_tlp_rumah_darurat', 30)->nullable();
			$table->string('no_ponsel_darurat', 30)->nullable();
			
			$table->integer('created_by');
			$table->integer('updated_by');
			$table->timestamps();
			
			$table->foreign('posisi')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
				  
			$table->foreign('asal_ktp')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
				  
			$table->foreign('suku_bangsa')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
				  
			$table->foreign('tempat_lahir')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
				  
			$table->foreign('agama')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
				  
			$table->foreign('kabupaten')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
				  
			$table->foreign('status_tempat_tinggal')
				  ->references('id')->on('params')
				  ->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('personal_datas');
	}
}
