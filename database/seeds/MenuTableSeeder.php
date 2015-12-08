<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		DB::table('menus')->truncate();
		
		DB::table('menus')->insert([
			[
				//'id' => 1,
				'name' => 'General Ledger',
				'title' => 'General Ledger',
				'api_endpoint' => null,
				'parent_id' => 0,
				'rank' => 1
			],
			[
				//'id' => 2,
				'name' => 'Taxes System',
				'title' => 'Taxes System',
				'api_endpoint' => null,
				'parent_id' => 0,
				'rank' => 2
			],
			[
				//'id' => 3,
				'name' => 'HR and Payroll',
				'title' => 'HR and Payroll',
				'api_endpoint' => null,
				'parent_id' => 0,
				'rank' => 3
			],
			[
				//'id' => 4,
				'name' => 'Project Control',
				'title' => 'Project Control',
				'api_endpoint' => null,
				'parent_id' => 0,
				'rank' => 4
			],
			[
				//'id' => 5,
				'name' => 'Report  System',
				'title' => 'Report  System',
				'api_endpoint' => null,
				'parent_id' => 0,
				'rank' => 5
			],
			[
				//'id' => 6,
				'name' => 'Setting',
				'title' => 'Setting',
				'api_endpoint' => null,
				'parent_id' => 0,
				'rank' => 6
			],
			
			
			
			[
				//'id' => 7,
				'name' => 'Account Code',
				'title' => 'Account Code',
				'api_endpoint' => null,
				'parent_id' => 1,
				'rank' => 1
			],
			[
				//'id' => 8,
				'name' => 'Jurnall Process',
				'title' => 'Jurnall Process',
				'api_endpoint' => '/jurnall_process',
				'parent_id' => 1,
				'rank' => 2
			],
			
			[
				//'id' => 9,
				'name' => 'PPh Ps 21',
				'title' => 'Taxes System: PPh Ps 21',
				'api_endpoint' => '/taxes/pph_21',
				'parent_id' => 2,
				'rank' => 1
			],
			[
				//'id' => 10,
				'name' => 'PPh Ps 25',
				'title' => 'Taxes System: PPh Ps 25',
				'api_endpoint' => '/taxes/pph_25',
				'parent_id' => 2,
				'rank' => 2
			],
			[
				//'id' => 11,
				'name' => 'PPN',
				'title' => 'Taxes System: PPN',
				'api_endpoint' => '/taxes/ppn',
				'parent_id' => 2,
				'rank' => 3
			],
			
			[
				//'id' => 12,
				'name' => 'Human Resource',
				'title' => 'Human Resource',
				'api_endpoint' => null,
				'parent_id' => 3,
				'rank' => 1
			],
			[
				//'id' => 13,
				'name' => 'Payroll',
				'title' => 'Payroll',
				'api_endpoint' => '/payroll',
				'parent_id' => 3,
				'rank' => 2
			],
			
			[
				//'id' => 14,
				'name' => 'Project Name',
				'title' => 'Project Name',
				'api_endpoint' => '/project_name',
				'parent_id' => 4,
				'rank' => 1
			],
			[
				//'id' => 15,
				'name' => 'Cash Flow',
				'title' => 'Cash Flow',
				'api_endpoint' => '/cash_flow',
				'parent_id' => 4,
				'rank' => 2
			],
			
			[
				//'id' => 16,
				'name' => 'All Financial Report',
				'title' => 'All Financial Report',
				'api_endpoint' => '/financial_report',
				'parent_id' => 5,
				'rank' => 1
			],
			[
				//'id' => 17,
				'name' => 'Balance Sheet',
				'title' => 'Balance Sheet',
				'api_endpoint' => '/balance_sheet',
				'parent_id' => 5,
				'rank' => 2
			],
			[
				//'id' => 18,
				'name' => 'Income Statement',
				'title' => 'Income Statement',
				'api_endpoint' => '/income_statement',
				'parent_id' => 5,
				'rank' => 3
			],
			[
				//'id' => 19,
				'name' => 'Cash Flow',
				'title' => 'Cash Flow',
				'api_endpoint' => '/cash_flow',
				'parent_id' => 5,
				'rank' => 4
			],
			[
				//'id' => 20,
				'name' => 'Financial Ratios',
				'title' => 'Financial Ratios',
				'api_endpoint' => '/financial_ratios',
				'parent_id' => 5,
				'rank' => 5
			],
			
			[
				//'id' => 21,
				'name' => 'User',
				'title' => 'User',
				'api_endpoint' => '/user',
				'parent_id' => 6,
				'rank' => 1
			],
			[
				//'id' => 22,
				'name' => 'Back Up System',
				'title' => 'Back Up System',
				'api_endpoint' => null,
				'parent_id' => 6,
				'rank' => 3
			],
			
			[
				//'id' => 23,
				'name' => 'Aktiva',
				'title' => 'Account Code: Aktiva',
				'api_endpoint' => '/account_code/aktiva',
				'parent_id' => 7,
				'rank' => 1
			],
			[
				//'id' => 24,
				'name' => 'Pasiva',
				'title' => 'Account Code: Pasiva',
				'api_endpoint' => '/account_code/pasiva',
				'parent_id' => 7,
				'rank' => 2
			],
			[
				//'id' => 25,
				'name' => 'Modal',
				'title' => 'Account Code: Modal',
				'api_endpoint' => '/account_code/modal',
				'parent_id' => 7,
				'rank' => 3
			],
			[
				//'id' => 26,
				'name' => 'Pendapatan',
				'title' => 'Account Code: Pendapatan',
				'api_endpoint' => '/account_code/pendapatan',
				'parent_id' => 7,
				'rank' => 4
			],
			[
				//'id' => 27,
				'name' => 'Biaya',
				'title' => 'Account Code: Biaya',
				'api_endpoint' => '/account_code/biaya',
				'parent_id' => 7,
				'rank' => 5
			],
			
			
			
			[
				//'id' => 28,
				'name' => 'HR Project',
				'title' => 'Human Resource: Project',
				'api_endpoint' => '/human_resource/hr-project',
				'parent_id' => 12,
				'rank' => 1
			],
			[
				//'id' => 29,
				'name' => 'HR Staf',
				'title' => 'Human Resource Staf',
				'api_endpoint' => '/human_resource/hr-staf',
				'parent_id' => 12,
				'rank' => 2
			],
			
			
			
			[
				//'id' => 30,
				'name' => 'Parameter',
				'title' => 'Parameter',
				'api_endpoint' => null,
				'parent_id' => 6,
				'rank' => 2
			],
			
			
			
			[
				//'id' => 31,
				'name' => 'Posisi Outsource',
				'title' => 'Posisi Outsource',
				'api_endpoint' => '/param/outsource-position',
				'parent_id' => 30,
				'rank' => 1
			],
			[
				//'id' => 32,
				'name' => 'Posisi Staff',
				'title' => 'Posisi Staff',
				'api_endpoint' => '/param/staff-position',
				'parent_id' => 30,
				'rank' => 2
			],
			[
				//'id' => 33,
				'name' => 'Agama',
				'title' => 'Agama',
				'api_endpoint' => '/param/religion',
				'parent_id' => 30,
				'rank' => 3
			],
			[
				//'id' => 34,
				'name' => 'Suku Bangsa',
				'title' => 'Suku Bangsa',
				'api_endpoint' => '/param/tribe',
				'parent_id' => 30,
				'rank' => 4
			],
			[
				//'id' => 35,
				'name' => 'Lokasi',
				'title' => 'Lokasi',
				'api_endpoint' => '/param/location',
				'parent_id' => 30,
				'rank' => 5
			],
			[
				//'id' => 36,
				'name' => 'Kota / Kabupaten',
				'title' => 'Kota / Kabupaten',
				'api_endpoint' => '/param/district',
				'parent_id' => 30,
				'rank' => 6
			],
			[
				//'id' => 37,
				'name' => 'Jenis Penyakit',
				'title' => 'Jenis Penyakit',
				'api_endpoint' => '/param/disease',
				'parent_id' => 30,
				'rank' => 7
			],
			[
				//'id' => 38,
				'name' => 'Pendidikan Formal',
				'title' => 'Pendidikan Formal',
				'api_endpoint' => '/param/formal-education',
				'parent_id' => 30,
				'rank' => 8
			],
			[
				//'id' => 39,
				'name' => 'Pelatihan',
				'title' => 'Pelatihan',
				'api_endpoint' => '/param/training',
				'parent_id' => 30,
				'rank' => 9
			],
			[
				//'id' => 40,
				'name' => 'Bahasa',
				'title' => 'Bahasa',
				'api_endpoint' => '/param/language',
				'parent_id' => 30,
				'rank' => 10
			],
			[
				//'id' => 41,
				'name' => 'Status Tinggal',
				'title' => 'Status Tempat Tiinggal',
				'api_endpoint' => '/param/status-of-residence',
				'parent_id' => 30,
				'rank' => 11
			],
			
			
		]);
	}
}
