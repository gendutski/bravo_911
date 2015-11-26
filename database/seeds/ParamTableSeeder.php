<?php

use Illuminate\Database\Seeder;

class ParamTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		//truncate
		DB::statement('truncate table params RESTART IDENTITY CASCADE');
		
		DB::table('params')->insert([
			[
				'name' => 'Islam',
				'type' => 'religion',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Kristen Katolik',
				'type' => 'religion',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Kristen Protestan',
				'type' => 'religion',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Hindu',
				'type' => 'religion',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Budha',
				'type' => 'religion',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			
			
			
			[
				'name' => 'Jantung',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Hipertensi',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Diabetes',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Hepartitis',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Kanker',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'TBC',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Asthma',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'AIDS',
				'type' => 'disease',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			
			
			
			[
				'name' => 'SD',
				'type' => 'formal-education',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'SMP',
				'type' => 'formal-education',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'SLTA/SMA',
				'type' => 'formal-education',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Diploma',
				'type' => 'formal-education',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'S-1',
				'type' => 'formal-education',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			
			
			
			[
				'name' => 'Beladiri',
				'type' => 'training',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Balakar',
				'type' => 'training',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Evakuasi',
				'type' => 'training',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			
			
			[
				'name' => 'Bahaasa Asing',
				'type' => 'language',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
			[
				'name' => 'Bahasa Daerah',
				'type' => 'language',
				'created_by' => 0,
				'updated_by' => 0,
				'created_at' => DB::raw('now()'),
				'updated_at' => DB::raw('now()')
			],
		]);
	}
}
