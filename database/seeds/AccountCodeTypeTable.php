<?php

use Illuminate\Database\Seeder;

class AccountCodeTypeTable extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('account_code_types')->truncate();
		DB::table('account_code_types')->insert([
			['name' => 'Aktiva'],
			['name' => 'Pasiva'],
			['name' => 'Modal'],
			['name' => 'Pendapatan'],
			['name' => 'Biaya']
		]);
	}
}
