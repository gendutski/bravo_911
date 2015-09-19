<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('users')->truncate();
		DB::table('users')->insert([
			'name' => 'Firman Darmawan',
			'email' => 'mvp.firman.darmawan@gmail.com',
			'password' => bcrypt('trial1234'),
			'created_at' => DB::raw('now()'),
			'updated_at' => DB::raw('now()')
		]);
	}
}
