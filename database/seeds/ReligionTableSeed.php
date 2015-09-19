<?php

use Illuminate\Database\Seeder;

class ReligionTableSeed extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('religions')->truncate();
		DB::table('religions')->insert([
			['name' => 'Islam'],
			['name' => 'Katolik'],
			['name' => 'Protestan'],
			['name' => 'Hindu'],
			['name' => 'Budha'],
			['name' => 'Konghucu'],
			['name' => 'Yahudi']
		]);
	}
}
