<?php

use Illuminate\Database\Seeder;

class PositionTypeTableSeed extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('position_types')->truncate();
		DB::table('position_types')->insert([
			['name' => 'HR Project', 'url_key' => 'hr_project'],
			['name' => 'HR Staf', 'url_key' => 'hr_staf']
		]);
	}
}
