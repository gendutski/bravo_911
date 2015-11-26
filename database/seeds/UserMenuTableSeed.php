<?php

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\User;

class UserMenuTableSeed extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('user_menu')->truncate();
		
		$user = User::find(1);
		$menus = Menu::get();
		
		foreach($menus as $row)
		{
			$user->assignMenu($row->id, true, true, true);
		}
	}
}
