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
			$table->string('nama_lengkap', 100);
			$table->string('asal_ktp', 50);
			$table->string('no_ktp', 30);
			$table->text('alamat_ktp');
			$table->date('masa_berlaku_ktp');
			$table->string('no_jamsostek', 30);
			$table->string('no_npwp', 30);
			$table->string('no_id_kta', 50)->nullable();
			$table->string('no_reg_kta', 50)->nullable();
			$table->string('suku_bangsa', 50);
			$table->string('alamat_email', 100);
			$table->boolean('status_menikah');
			$table->smallInteger('jumlah_anak');
			$table->string('tempat_lahir', 100);
			$table->date('tgl_lahir');
			$table->enum('jenis_kelamin', ['Pria', 'Wanita']);
			$table->integer('religion_id');
			$table->smallInteger('tinggi_badan');
			$table->smallInteger('berat_badan');
			$table->text('alamat_tinggal');
			$table->string('kota', 100);
			$table->string('status_tempat_tinggal', 100);
			$table->string('no_telp_rumah', 20);
			$table->string('no_hp', 20);
			$table->string('photo', 100)->nullable();
			
			$table->string('nama_pasangan', 100)->nullable();
			$table->string('tempat_lahir_pasangan', 100)->nullable();
			$table->date('tgl_lahir_pasangan')->nullable();
			$table->text('alamat_pasangan')->nullable();
			$table->boolean('tanggung_jawab_pasangan')->nullable();
			
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
		Schema::drop('personal_datas');
	}
}
