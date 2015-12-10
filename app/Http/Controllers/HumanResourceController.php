<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Param;
use App\Models\PersonalData;
use App\Models\PersonalFamily;
use DB;

class HumanResourceController extends Controller
{
	private $menu_id = array(
		'hr-project' => 28,
		'hr-staf' => 29,
	);

	public function getPersonal(Request $request)
	{
		//request
		$type = $request->input('type');
		$redirect_url = $request->input('redirect');
		
		//tidak punya akses post forbidden
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'post'))
		{
			return response('forbidden', 403);
		}
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		$menu_name = $menu->name;
		
		//params
		$params = Param::orderBy('type')
			->orderBy('name')
			->get();
		
		$dropdown['religion'] = array();
		$dropdown['position'] = array();
		$dropdown['tribe'] = array();
		$dropdown['location'] = array();
		$dropdown['disease'] = array();
		$dropdown['formal-education'] = array();
		$dropdown['training'] = array();
		$dropdown['language'] = array();
		$dropdown['district'] = array();
		$dropdown['status-of-residence'] = array();
		
		foreach($params	as $row)
		{
			if(
				($row->type == 'outsource-position' && $type == 'hr-project') ||
				($row->type == 'staff-position' && $type == 'hr-staf')
			)
			{
				$dropdown['position'][] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif(array_key_exists($row->type, $dropdown))
			{
				$dropdown[$row->type][] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
		}
		
		//type
		$return_result['type'] = 'input form';
		
		//form attribute
		$return_result['form']['attr'] = [
			'method' => 'post',
			'action' => url('human_resource/personal?type='.$type),
			'role' => 'form'
		];
			
		//form hidden elements
		$return_result['form']['hidden'] = [
			['name' => '_token', 'value' => csrf_token()],
			['name' => '_redirect', 'value' => $redirect_url]
		];
		
		//form visible elements
		$return_result['form']['elements'] = array();
		
		//block 1
		$return_result['form']['elements'][0][0][] = [
			'label' => 'Posisi Yang Dilamar', 
			'element' => 'select',
			'attr' => [
				'name' => 'posisi',
			],
			'options' => $dropdown['position']
		];
		
		//block 2 void
		$return_result['form']['elements'][0][1] = array();
		
		//block 3 sub header
		$return_result['form']['elements'][1][0][] = [
			'label' => 'DATA PRIBADI', 
			'element' => 'header',
		];
		
		//block 4
		$return_result['form']['elements'][2][0] = [
			[
				'label' => 'Nama Lengkap', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'nama_lengkap',
					'maxlength' => 255
				]
			],
			[
				'label' => 'Asal KTP', 
				'element' => 'select',
				'attr' => [
					'name' => 'asal_ktp',
				],
				'options' => $dropdown['location']
			],
			[
				'label' => 'No KTP', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'no_ktp',
					'maxlength' => 100
				]
			],
			[
				'label' => 'Alamat di KTP', 
				'element' => 'textarea',
				'attr' => [
					'name' => 'alamat_ktp',
				]
			],
			[
				'label' => 'Masa Berlaku KTP', 
				'element' => 'datepicker',
				'attr' => [
					'name' => 'masa_berlaku_ktp',
				]
			],
			[
				'label' => 'No-Jamsostek', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'no_jamsostek',
					'maxlength' => 100
				]
			],
			[
				'label' => 'No-NPWP', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'no_npwp',
					'maxlength' => 100
				]
			],
			[
				'label' => 'No-ID KTA Security', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'no_id_kta_security',
					'maxlength' => 100
				]
			],
			[
				'label' => 'No-Reg KTA Security', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'no_reg_kta_security',
					'maxlength' => 100
				]
			],
			[
				'label' => 'Suku Bangsa', 
				'element' => 'select',
				'attr' => [
					'name' => 'suku_bangsa',
				],
				'options' => $dropdown['tribe']
			],
			[
				'label' => 'Alamat Email', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'email',
					'maxlength' => 255
				]
			],
			[
				'label' => 'Status Perkawinan', 
				'element' => 'select',
				'attr' => [
					'name' => 'status_menikah',
				],
				'options' => [
					[
						'attr' => ['value' => 'tidak'],
						'html' => 'Belum Menikah'
					],
					[
						'attr' => ['value' => 'ya'],
						'html' => 'Menikah'
					],
				]
			],
		];
		
		//block 5
		$return_result['form']['elements'][2][1] = [
			[
				'label' => 'Tempat Lahir', 
				'element' => 'select',
				'attr' => [
					'name' => 'tempat_lahir',
				],
				'options' => $dropdown['location']
			],
			[
				'label' => 'Tanggal Lahir', 
				'element' => 'datepicker',
				'attr' => [
					'name' => 'tgl_lahir',
				]
			],
			[
				'label' => 'Jenis Kelamin', 
				'element' => 'select',
				'attr' => [
					'name' => 'jenis_kelamin',
				],
				'options' => [
					[
						'attr' => ['value' => 'pria'],
						'html' => 'Pria'
					],
					[
						'attr' => ['value' => 'wanita'],
						'html' => 'Wanita'
					],
				]
			],
			[
				'label' => 'Agama', 
				'element' => 'select',
				'attr' => [
					'name' => 'agama',
				],
				'options' => $dropdown['religion']
			],
			[
				'label' => 'Tinggi Badan', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'tinggi_badan',
					'placeholder' => 'Dalam cm'
				]
			],
			[
				'label' => 'Berat Badan', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'berat_badan',
					'placeholder' => 'Dalam Kg'
				]
			],
			[
				'label' => 'Alamat Tinggal Sekarang', 
				'element' => 'textarea',
				'attr' => [
					'name' => 'alamat_tinggal_sekarang',
				]
			],
			[
				'label' => 'Kota / Kabupaten', 
				'element' => 'select',
				'attr' => [
					'name' => 'kabupaten',
				],
				'options' => $dropdown['district']
			],
			[
				'label' => 'Status Tmpt Tinggal', 
				'element' => 'select',
				'attr' => [
					'name' => 'status_tempat_tinggal',
				],
				'options' => $dropdown['status-of-residence']
			],
			[
				'label' => 'No-Call Rumah', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'no_call_rumah',
					'maxlength' => 30
				],
			],
			[
				'label' => 'No-Contak Person', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'no_contact_person',
					'maxlength' => 30
				],
			],
		];
		
		//block 6 sub header
		$return_result['form']['elements'][3][0][] = [
			'label' => 'DATA KELUARGA', 
			'element' => 'header',
		];
		
		
		//block 7 suami istri
		$return_result['form']['elements'][4][0] = [
			[
				'label' => 'Nama Suami/Istri', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'nama_pasangan',
					'maxlength' => 255
				],
			],
			[
				'label' => 'Tempat Lahir', 
				'element' => 'select',
				'attr' => [
					'name' => 'tempat_lahir_pasangan',
				],
				'options' => $dropdown['location']
			],
			[
				'label' => 'Tanggal Lahir', 
				'element' => 'datepicker',
				'attr' => [
					'name' => 'tgl_lahir_pasangan',
				]
			],
			[
				'label' => 'Pekerjaan Suami/Istri', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'pekerjaan_pasangan',
					'maxlength' => 255
				],
			],
			[
				'label' => 'Apakah Suami/Istri Menjadi Tanggung Jawab Anda?', 
				'element' => 'select',
				'attr' => [
					'name' => 'tanggungan_pasangan',
				],
				'options' => [
					[
						'attr' => ['value' => 'ya'],
						'html' => 'Ya'
					],
					[
						'attr' => ['value' => 'tidak'],
						'html' => 'Tidak'
					],
				]
			],
		];
		
		//block 8 alamat suami istri
		$return_result['form']['elements'][4][1][] = [
			'label' => 'Alamat Suami/Istri', 
			'element' => 'textarea',
			'attr' => [
				'name' => 'alamat_pasangan',
				'rows' => 8
			]
		];
		
		
		//block 9 anak
		for($i = 0; $i < 3; $i++)
		{
			$return_result['form']['elements'][5][0][] = [
				'label' => 'Nama Anak #'.($i+1), 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'nama_anak[]',
					'maxlength' => 255
				],
			];
			
			$return_result['form']['elements'][5][1][] = [
				'label' => 'Tempat Lahir', 
				'element' => 'select',
				'attr' => [
					'name' => 'tempat_lahir_anak[]',
				],
				'options' => $dropdown['location']
			];
			
			$return_result['form']['elements'][5][2][] = [
				'label' => 'Tanggal Lahir', 
				'element' => 'datepicker',
				'attr' => [
					'name' => 'tgl_lahir_anak[]',
				]
			];
		}
		
		//block 10 line
		$return_result['form']['elements'][6][0][] = [
			'label' => '&nbsp;', 
			'element' => 'header',
		];
		
		//block 11 ayah, ibu, saudara
		$arr_saudara = ['Ayah', 'Ibu', 'Kakak/Adik', 'Kakak/Adik'];
		$arr_elm_saudara = ['ayah', 'ibu', 'saudara', 'saudara'];
		for($i = 0; $i < 4; $i++)
		{
			$return_result['form']['elements'][7][0][] = [
				'label' => 'Nama '.$arr_saudara[$i], 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'nama_'.$arr_elm_saudara[$i].'[]',
					'maxlength' => 255
				],
			];
			
			$return_result['form']['elements'][7][1][] = [
				'label' => 'Pekerjaan', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'pekerjaan_'.$arr_elm_saudara[$i].'[]',
					'maxlength' => 255
				],
			];
			
			$return_result['form']['elements'][7][2][] = [
				'label' => 'Alamat', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'alamat_'.$arr_elm_saudara[$i].'[]',
				],
			];
		}
		
		//block 12
		$return_result['form']['elements'][8][0][] = [
			'label' => 'Anak Ke', 
			'element' => 'input',
			'attr' => [
				'type' => 'text',
				'name' => 'anak_ke',
			],
		];
		
		//block 13
		$return_result['form']['elements'][8][1][] = [
			'label' => 'Jumlah Saudara', 
			'element' => 'input',
			'attr' => [
				'type' => 'text',
				'name' => 'jumlah_saudara',
			],
		];
		
		//block 14 darurat
		$return_result['form']['elements'][9][0][] = [
			'label' => 'Yang Harus di Hubungi dalam Keadaan Darurat (Tidak Serumah)', 
			'element' => 'header',
		];
		
		//block 15
		$return_result['form']['elements'][10][0] = [
			[
				'label' => 'Nama', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'nama_darurat',
					'maxlength' => 255
				],
			],
			[
				'label' => 'Hubungan', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'hubungan_darurat',
					'maxlength' => 255
				],
			],
			[
				'label' => 'Pekerjaan', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'pekerjaan_darurat',
					'maxlength' => 255
				],
			],
			[
				'label' => 'Alamat', 
				'element' => 'textarea',
				'attr' => [
					'name' => 'alamat_darurat',
				],
			],
			[
				'label' => 'No-Tlp.Rumah Darurat', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'telp_darurat',
					'maxlength' => 20
				],
			],
			[
				'label' => 'No.Ponsel/Darurat', 
				'element' => 'input',
				'attr' => [
					'type' => 'text',
					'name' => 'ponsel_darurat',
					'maxlength' => 20
				],
			],

		];
		
		
		//submit
		$return_result['form']['elements'][11][0] = [
			[
				'label' => 'Submit', 
				'element' => 'button',
				'attr' => [
					'type' => 'submit', 
					'class' => 'btn btn-primary',
					'style' => 'margin-right:5px'
				]
			],
			[
				'label' => 'Reset', 
				'element' => 'button',
				'attr' => [
					'type' => 'reset', 
					'class' => 'btn btn-warning',
					'style' => 'margin-right:5px'
				]
			],
			[
				'label' => '<i class="glyphicon glyphicon-circle-arrow-left"></i> Back', 
				'element' => 'button',
				'attr' => [
					'type' => 'button', 
					'class' => 'btn btn-success',
					'style' => 'margin-right:5px',
					'data-title' => $menu_title,
					'data-endpoint' => $redirect_url,
					'data-method' => 'GET'
				]
			]
		];
		
		$return_result['form_data'] = [];
		return response($return_result, 200);
	}

	public function getEditPersonal(Request $request, $id)
	{
		//request
		$redirect_url = $request->input('redirect');
		$type = $request->input('type');
		
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'put'))
		{
			return response('Forbidden', 403);
		}
		
		$pd = PersonalData::findOrFail($id);
		$check_position = $pd->position->type;
		if(
			($type == 'hr-project' && $check_position != 'outsource-position') ||
			($type == 'hr-staf' && $check_position != 'staff-position') 
		){ return response('not found', 404); }
		
		$return_result = $this->getPersonal($request)->original;
		
		//set action
		$return_result['form']['attr']['action'] = url('human_resource/personal/'.$id.'?type='.$type);
		
		//set method
		$return_result['form']['attr']['method'] = 'put';
		
		$return_result['form_data'] = [
			'posisi' => $pd->posisi,
			'nama_lengkap' => $pd->nama_lengkap,
			'asal_ktp' => $pd->asal_ktp,
			'no_ktp' => $pd->no_ktp,
			'alamat_ktp' => $pd->alamat_ktp,
			'masa_berlaku_ktp' => $pd->masa_berlaku_ktp,
			'no_jamsostek' => $pd->no_jamsostek,
			'no_npwp' => $pd->no_npwp,
			'no_id_kta_security' => $pd->no_id_kta_security,
			'no_reg_kta_security' => $pd->no_reg_kta_security,
			'suku_bangsa' => $pd->suku_bangsa,
			'email' => $pd->email,
			'status_menikah' => $pd->status_menikah? 'ya':'tidak',
			'tempat_lahir' => $pd->tempat_lahir,
			'tgl_lahir' => $pd->tgl_lahir,
			'jenis_kelamin' => $pd->jenis_kelamin,
			'agama' => $pd->agama,
			'tinggi_badan' => $pd->tinggi_badan,
			'berat_badan' => $pd->berat_badan,
			'alamat_tinggal_sekarang' => $pd->alamat_tinggal_sekarang,
			'kabupaten' => $pd->kabupaten,
			'status_tempat_tinggal' => $pd->status_tempat_tinggal,
			'no_call_rumah' => $pd->no_call_rumah,
			'no_contact_person' => $pd->no_contact_person,
			'tanggungan_pasangan' => $pd->tanggungan_pasangan? 'ya':'tidak',
			'nama_darurat' => $pd->nama_darurat,
			'hubungan_darurat' => $pd->hubungan_darurat,
			'pekerjaan_darurat' => $pd->pekerjaan_darurat,
			'alamat_darurat' => $pd->alamat_darurat,
			'telp_darurat' => $pd->telp_darurat,
			'ponsel_darurat' => $pd->ponsel_darurat,
			'anak_ke' => $pd->anak_ke,
			'jumlah_saudara' => $pd->jumlah_saudara,
		];
		
		//family
		$family = $pd->family;
		
		foreach($family as $row)
		{
			if($row->tipe == 'suami-istri')
			{
				$return_result['form_data']['nama_pasangan'] = $row->nama;
				$return_result['form_data']['tempat_lahir_pasangan'] = $row->tempat_lahir;
				$return_result['form_data']['tgl_lahir_pasangan'] = $row->tgl_lahir;
				$return_result['form_data']['pekerjaan_pasangan'] = $row->pekerjaan;
				$return_result['form_data']['alamat_pasangan'] = $row->alamat;
			}
			elseif($row->tipe == 'anak')
			{
				$return_result['form_data']['nama_anak[]'][] = $row->nama;
				$return_result['form_data']['tempat_lahir_anak[]'][] = $row->tempat_lahir;
				$return_result['form_data']['tgl_lahir_anak[]'][] = $row->tgl_lahir;
			}
			elseif($row->tipe == 'ayah')
			{
				$return_result['form_data']['nama_ayah[]'] = $row->nama;
				$return_result['form_data']['pekerjaan_ayah[]'] = $row->pekerjaan;
				$return_result['form_data']['alamat_ayah[]'] = $row->alamat;
			}
			elseif($row->tipe == 'ibu')
			{
				$return_result['form_data']['nama_ibu[]'] = $row->nama;
				$return_result['form_data']['pekerjaan_ibu[]'] = $row->pekerjaan;
				$return_result['form_data']['alamat_ibu[]'] = $row->alamat;
			}
			elseif($row->tipe == 'adik-kakak')
			{
				$return_result['form_data']['nama_saudara[]'] = $row->nama;
				$return_result['form_data']['pekerjaan_saudara[]'] = $row->pekerjaan;
				$return_result['form_data']['alamat_saudara[]'] = $row->alamat;
			}
		}
		
		return response($return_result, 200);
	}
	
	public function postPersonal(Request $request)
	{
		//request
		$type = $request->input('type');
		$redirect_url = $request->input('redirect');
		
		//check privilege
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'post'))
		{
			return response('Forbidden', 403);
		}
		
		//validate
		$this->validate(
			$request, 
			[
				'posisi' => 'required',
				'nama_lengkap' => 'required|max:255',
				'asal_ktp' => 'required',
				'no_ktp' => 'required|max:100',
				'alamat_ktp' => 'required',
				'masa_berlaku_ktp' => 'required',
				'suku_bangsa' => 'required',
				'email' => 'required|max:255|unique:personal_datas,email',
				'status_menikah' => 'required',
				'tempat_lahir' => 'required',
				'tgl_lahir' => 'required',
				'jenis_kelamin' => 'required',
				'agama' => 'required',
				'tinggi_badan' => 'required|numeric',
				'berat_badan' => 'required|numeric',
				'alamat_tinggal_sekarang' => 'required',
				'kabupaten' => 'required',
				'status_tempat_tinggal' => 'required',
				'telp_darurat' => 'required',
				'ponsel_darurat' => 'required',
				
				'nama_pasangan' => 'required_if:status_menikah,ya',
				'tempat_lahir_pasangan' => 'required_if:status_menikah,ya',
				'tgl_lahir_pasangan' => 'required_if:status_menikah,ya',
				'pekerjaan_pasangan' => 'required_if:status_menikah,ya',
				'tanggungan_pasangan' => 'required_if:status_menikah,ya',
				'alamat_pasangan' => 'required_if:status_menikah,ya',
				
				'anak_ke' => 'numeric',
				'jumlah_saudara' => 'numeric',
			],
			[
				'posisi.required' => '<span style="font-weight:bold;font-style:italic">Posisi</span> harap di isi',
				'nama_lengkap.required' => '<span style="font-weight:bold;font-style:italic">Nama Lengkap</span> harap di isi',
				'asal_ktp.required' => '<span style="font-weight:bold;font-style:italic">Asal KTP</span> harap di isi',
				'no_ktp.required' => '<span style="font-weight:bold;font-style:italic">No KTP</span> harap di isi',
				'alamat_ktp.required' => '<span style="font-weight:bold;font-style:italic">Alamat di KTP</span> harap di isi',
				'masa_berlaku_ktp.required' => '<span style="font-weight:bold;font-style:italic">Masa Berlaku KTP</span> harap di isi',
				'suku_bangsa.required' => '<span style="font-weight:bold;font-style:italic">Suku Bangsa</span> harap di isi',
				'email.required' => '<span style="font-weight:bold;font-style:italic">Alamat Email</span> harap di isi',
				'email.unique' => '<span style="font-weight:bold;font-style:italic">'.$request->input('email').'</span> sudah terdaftar. Harap pilih alamat email yang lain!',
				'status_menikah.required' => '<span style="font-weight:bold;font-style:italic">Status Perkawinan</span> harap di isi',
				'tempat_lahir.required' => '<span style="font-weight:bold;font-style:italic">Tempat Lahir</span> harap di isi',
				'tgl_lahir.required' => '<span style="font-weight:bold;font-style:italic">Tanggal Lahir</span> harap di isi',
				'jenis_kelamin.required' => '<span style="font-weight:bold;font-style:italic">Jenis Kelamin</span> harap di isi',
				'agama.required' => '<span style="font-weight:bold;font-style:italic">Agama</span> harap di isi',
				'tinggi_badan.required' => '<span style="font-weight:bold;font-style:italic">Tinggi Badan</span> harap di isi',
				'tinggi_badan.numeric' => '<span style="font-weight:bold;font-style:italic">Tinggi Badan</span> harap di isi angka',
				'berat_badan.required' => '<span style="font-weight:bold;font-style:italic">Berat Badan</span> harap di isi',
				'berat_badan.numeric' => '<span style="font-weight:bold;font-style:italic">Berat Badan</span> harap di isi angka',
				'alamat_tinggal_sekarang.required' => '<span style="font-weight:bold;font-style:italic">Alamat Tinggal Sekarang</span> harap di isi',
				'kabupaten.required' => '<span style="font-weight:bold;font-style:italic">Kota / Kabupaten</span> harap di isi',
				'status_tempat_tinggal.required' => '<span style="font-weight:bold;font-style:italic">Status Tmpt Tinggal</span> harap di isi',
				'telp_darurat.required' => '<span style="font-weight:bold;font-style:italic">No-Tlp.Rumah Darurat</span> harap di isi',
				'ponsel_darurat.required' => '<span style="font-weight:bold;font-style:italic">No.Ponsel/Darurat</span> harap di isi',
				
				'nama_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Nama Suami/Istri</span> harap di isi',
				'tempat_lahir_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Tempat Lahir Suami/Istri</span> harap di isi',
				'tgl_lahir_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Tanggal Lahir Suami/Istri</span> harap di isi',
				'pekerjaan_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Pekerjaan Suami/Istri</span> harap di isi',
				'tanggungan_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Apakah Suami/Istri Menjadi Tanggung Jawab Anda?</span> harap di isi',
				'alamat_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Alamat Suami/Istri</span> harap di isi',
				
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		
		//hitung anak
		$arr_anak = array();
		$nama_anak = $request->input('nama_anak');
		$tempat_lahir_anak = $request->input('tempat_lahir_anak');
		$tgl_lahir_anak = $request->input('tgl_lahir_anak');
		for($i = 0; $i < count($request->input('nama_anak')); $i++)
		{
			if(!empty($nama_anak[$i]) && !empty($tempat_lahir_anak[$i]) && !empty($tgl_lahir_anak[$i]))
			{
				$arr_anak[] = [$nama_anak[$i], $tempat_lahir_anak[$i], $tgl_lahir_anak[$i]];
			}
		}
		
		//save personal
		$pd = new PersonalData;
		$pd->nama_lengkap = $request->input('nama_lengkap');
		$pd->posisi = $request->input('posisi');
		$pd->asal_ktp = $request->input('asal_ktp');
		$pd->no_ktp = $request->input('no_ktp');
		$pd->alamat_ktp = $request->input('alamat_ktp');
		$pd->masa_berlaku_ktp = $request->input('masa_berlaku_ktp');
		$pd->no_jamsostek = $request->input('no_jamsostek', null);
		$pd->no_npwp = $request->input('no_npwp', null);
		$pd->no_id_kta_security = $request->input('no_id_kta_security', null);
		$pd->no_reg_kta_security = $request->input('no_reg_kta_security', null);
		$pd->suku_bangsa = $request->input('suku_bangsa');
		$pd->email = $request->input('email');
		$pd->status_menikah = $request->input('status_menikah') == 'ya'? true:false;
		$pd->tempat_lahir = $request->input('tempat_lahir');
		$pd->tgl_lahir = $request->input('tgl_lahir');
		$pd->jenis_kelamin = $request->input('jenis_kelamin');
		$pd->agama = $request->input('agama');
		$pd->tinggi_badan = $request->input('tinggi_badan');
		$pd->berat_badan = $request->input('berat_badan');
		$pd->alamat_tinggal_sekarang = $request->input('alamat_tinggal_sekarang');
		$pd->kabupaten = $request->input('kabupaten');
		$pd->status_tempat_tinggal = $request->input('status_tempat_tinggal');
		$pd->no_call_rumah = $request->input('no_call_rumah');
		$pd->no_contact_person = $request->input('no_contact_person');
		$pd->tanggungan_pasangan = $request->input('tanggungan_pasangan') == 'ya'? true:false;
		$pd->nama_darurat = $request->input('nama_darurat');
		$pd->jumlah_anak = count($arr_anak);
		$pd->anak_ke = $request->input('anak_ke');
		$pd->jumlah_saudara = $request->input('jumlah_saudara');
		$pd->nama_darurat = $request->input('nama_darurat', null);
		$pd->hubungan_darurat = $request->input('hubungan_darurat', null);
		$pd->pekerjaan_darurat = $request->input('pekerjaan_darurat', null);
		$pd->alamat_darurat = $request->input('alamat_darurat', null);
		$pd->telp_darurat = $request->input('telp_darurat');
		$pd->ponsel_darurat = $request->input('ponsel_darurat');
		
		$pd->created_by = $request->user()->id;
		$pd->updated_by = $request->user()->id;
		if($pd->save())
		{
			
			//keluarga 1
			if($pd->status_menikah)
			{
				//save istri
				$pf = new PersonalFamily;
				$pf->personal_id = $pd->id;
				$pf->tipe = 'suami-istri';
				$pf->nama = $request->input('nama_pasangan');
				$pf->tempat_lahir = $request->input('tempat_lahir_pasangan');
				$pf->tgl_lahir = $request->input('tgl_lahir_pasangan');
				$pf->alamat = $request->input('alamat_pasangan');
				$pf->pekerjaan = $request->input('pekerjaan_pasangan');
				
				$pf->created_by = $request->user()->id;
				$pf->updated_by = $request->user()->id;
				$pf->save();
				
				//save anak
				if(!empty($arr_anak))
				{
					foreach($arr_anak as $anak)
					{
						$pf = new PersonalFamily;
						$pf->personal_id = $pd->id;
						$pf->tipe = 'anak';
						$pf->nama = $anak[0];
						$pf->tempat_lahir = $anak[1];
						$pf->tgl_lahir = $anak[2];
						
						$pf->created_by = $request->user()->id;
						$pf->updated_by = $request->user()->id;
						$pf->save();
					}
				}
			}
			
			//ayah
			$nama_ayah = $request->input('nama_ayah');
			if(!empty($nama_ayah) && is_array($nama_ayah))
			{
				$alamat_ayah = $request->input('alamat_ayah');
				$pekerjaan_ayah = $request->input('pekerjaan_ayah');
				
				$pf = new PersonalFamily;
				$pf->personal_id = $pd->id;
				$pf->tipe = 'ayah';
				$pf->nama = $nama_ayah[0];
				$pf->alamat = (!empty($alamat_ayah) && is_array($alamat_ayah))? $alamat_ayah[0]:null;
				$pf->pekerjaan = (!empty($pekerjaan_ayah) && is_array($pekerjaan_ayah))? $pekerjaan_ayah[0]:null;
				$pf->created_by = $request->user()->id;
				$pf->updated_by = $request->user()->id;
				$pf->save();
			}
			
			//ibu
			$nama_ibu = $request->input('nama_ibu');
			if(!empty($nama_ibu) && is_array($nama_ibu))
			{
				$alamat_ibu = $request->input('alamat_ibu');
				$pekerjaan_ibu = $request->input('pekerjaan_ibu');
				
				$pf = new PersonalFamily;
				$pf->personal_id = $pd->id;
				$pf->tipe = 'ibu';
				$pf->nama = $nama_ibu[0];
				$pf->alamat = (!empty($alamat_ibu) && is_array($alamat_ibu))? $alamat_ibu[0]:null;
				$pf->pekerjaan = (!empty($pekerjaan_ibu) && is_array($pekerjaan_ibu))? $pekerjaan_ibu[0]:null;
				$pf->created_by = $request->user()->id;
				$pf->updated_by = $request->user()->id;
				$pf->save();
			}
			
			//saudara
			$nama_saudara = $request->input('nama_saudara');
			if(!empty($nama_saudara) && is_array($nama_saudara))
			{
				$alamat_saudara = $request->input('alamat_saudara');
				$pekerjaan_saudara = $request->input('pekerjaan_saudara');
				
				for($i = 0; $i < count($nama_saudara); $i++)
				{
					$pf = new PersonalFamily;
					$pf->personal_id = $pd->id;
					$pf->tipe = 'adik-kakak';
					$pf->nama = $nama_saudara[$i];
					$pf->alamat = !empty($alamat_saudara[$i])? $alamat_saudara[$i]:null;
					$pf->pekerjaan = !empty($pekerjaan_saudara[$i])? $pekerjaan_saudara[$i]:null;
					$pf->created_by = $request->user()->id;
					$pf->updated_by = $request->user()->id;
					$pf->save();
				}
			}
			
			return response([
				'url' => '',
				'api_endpoint' => url('human_resource/'.$type),
				'api_method' => 'GET',
				'title' => $menu_title
			], 200);
		}
		
		return response('bad request', 400);
	}
	
	public function putPersonal(Request $request, $id)
	{
		//request
		$type = $request->input('type');
		$redirect_url = $request->input('redirect');
		
		//check privilege
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'post'))
		{
			return response('Forbidden', 403);
		}
		
		//validate
		$this->validate(
			$request, 
			[
				'posisi' => 'required',
				'nama_lengkap' => 'required|max:255',
				'asal_ktp' => 'required',
				'no_ktp' => 'required|max:100',
				'alamat_ktp' => 'required',
				'masa_berlaku_ktp' => 'required',
				'suku_bangsa' => 'required',
				'email' => 'required|max:255|unique:personal_datas,email',
				'status_menikah' => 'required',
				'tempat_lahir' => 'required',
				'tgl_lahir' => 'required',
				'jenis_kelamin' => 'required',
				'agama' => 'required',
				'tinggi_badan' => 'required|numeric',
				'berat_badan' => 'required|numeric',
				'alamat_tinggal_sekarang' => 'required',
				'kabupaten' => 'required',
				'status_tempat_tinggal' => 'required',
				'telp_darurat' => 'required',
				'ponsel_darurat' => 'required',
				
				'nama_pasangan' => 'required_if:status_menikah,ya',
				'tempat_lahir_pasangan' => 'required_if:status_menikah,ya',
				'tgl_lahir_pasangan' => 'required_if:status_menikah,ya',
				'pekerjaan_pasangan' => 'required_if:status_menikah,ya',
				'tanggungan_pasangan' => 'required_if:status_menikah,ya',
				'alamat_pasangan' => 'required_if:status_menikah,ya',
				
				'anak_ke' => 'numeric',
				'jumlah_saudara' => 'numeric',
			],
			[
				'posisi.required' => '<span style="font-weight:bold;font-style:italic">Posisi</span> harap di isi',
				'nama_lengkap.required' => '<span style="font-weight:bold;font-style:italic">Nama Lengkap</span> harap di isi',
				'asal_ktp.required' => '<span style="font-weight:bold;font-style:italic">Asal KTP</span> harap di isi',
				'no_ktp.required' => '<span style="font-weight:bold;font-style:italic">No KTP</span> harap di isi',
				'alamat_ktp.required' => '<span style="font-weight:bold;font-style:italic">Alamat di KTP</span> harap di isi',
				'masa_berlaku_ktp.required' => '<span style="font-weight:bold;font-style:italic">Masa Berlaku KTP</span> harap di isi',
				'suku_bangsa.required' => '<span style="font-weight:bold;font-style:italic">Suku Bangsa</span> harap di isi',
				'email.required' => '<span style="font-weight:bold;font-style:italic">Alamat Email</span> harap di isi',
				'email.unique' => '<span style="font-weight:bold;font-style:italic">'.$request->input('email').'</span> sudah terdaftar. Harap pilih alamat email yang lain!',
				'status_menikah.required' => '<span style="font-weight:bold;font-style:italic">Status Perkawinan</span> harap di isi',
				'tempat_lahir.required' => '<span style="font-weight:bold;font-style:italic">Tempat Lahir</span> harap di isi',
				'tgl_lahir.required' => '<span style="font-weight:bold;font-style:italic">Tanggal Lahir</span> harap di isi',
				'jenis_kelamin.required' => '<span style="font-weight:bold;font-style:italic">Jenis Kelamin</span> harap di isi',
				'agama.required' => '<span style="font-weight:bold;font-style:italic">Agama</span> harap di isi',
				'tinggi_badan.required' => '<span style="font-weight:bold;font-style:italic">Tinggi Badan</span> harap di isi',
				'tinggi_badan.numeric' => '<span style="font-weight:bold;font-style:italic">Tinggi Badan</span> harap di isi angka',
				'berat_badan.required' => '<span style="font-weight:bold;font-style:italic">Berat Badan</span> harap di isi',
				'berat_badan.numeric' => '<span style="font-weight:bold;font-style:italic">Berat Badan</span> harap di isi angka',
				'alamat_tinggal_sekarang.required' => '<span style="font-weight:bold;font-style:italic">Alamat Tinggal Sekarang</span> harap di isi',
				'kabupaten.required' => '<span style="font-weight:bold;font-style:italic">Kota / Kabupaten</span> harap di isi',
				'status_tempat_tinggal.required' => '<span style="font-weight:bold;font-style:italic">Status Tmpt Tinggal</span> harap di isi',
				'telp_darurat.required' => '<span style="font-weight:bold;font-style:italic">No-Tlp.Rumah Darurat</span> harap di isi',
				'ponsel_darurat.required' => '<span style="font-weight:bold;font-style:italic">No.Ponsel/Darurat</span> harap di isi',
				
				'nama_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Nama Suami/Istri</span> harap di isi',
				'tempat_lahir_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Tempat Lahir Suami/Istri</span> harap di isi',
				'tgl_lahir_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Tanggal Lahir Suami/Istri</span> harap di isi',
				'pekerjaan_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Pekerjaan Suami/Istri</span> harap di isi',
				'tanggungan_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Apakah Suami/Istri Menjadi Tanggung Jawab Anda?</span> harap di isi',
				'alamat_pasangan.required_if' => '<span style="font-weight:bold;font-style:italic">Alamat Suami/Istri</span> harap di isi',
				
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		
		//cek data
		$pd = PersonalData::findOrFail($id);
		$check_position = $pd->position->type;
		if(
			($type == 'hr-project' && $check_position != 'outsource-position') ||
			($type == 'hr-staf' && $check_position != 'staff-position') 
		){ return response('not found', 404); }
		
		//hitung anak
		$arr_anak = array();
		$nama_anak = $request->input('nama_anak');
		$tempat_lahir_anak = $request->input('tempat_lahir_anak');
		$tgl_lahir_anak = $request->input('tgl_lahir_anak');
		for($i = 0; $i < count($request->input('nama_anak')); $i++)
		{
			if(!empty($nama_anak[$i]) && !empty($tempat_lahir_anak[$i]) && !empty($tgl_lahir_anak[$i]))
			{
				$arr_anak[] = [$nama_anak[$i], $tempat_lahir_anak[$i], $tgl_lahir_anak[$i]];
			}
		}
		
		//save personal
		$pd->nama_lengkap = $request->input('nama_lengkap');
		$pd->posisi = $request->input('posisi');
		$pd->asal_ktp = $request->input('asal_ktp');
		$pd->no_ktp = $request->input('no_ktp');
		$pd->alamat_ktp = $request->input('alamat_ktp');
		$pd->masa_berlaku_ktp = $request->input('masa_berlaku_ktp');
		$pd->no_jamsostek = $request->input('no_jamsostek', null);
		$pd->no_npwp = $request->input('no_npwp', null);
		$pd->no_id_kta_security = $request->input('no_id_kta_security', null);
		$pd->no_reg_kta_security = $request->input('no_reg_kta_security', null);
		$pd->suku_bangsa = $request->input('suku_bangsa');
		$pd->email = $request->input('email');
		$pd->status_menikah = $request->input('status_menikah') == 'ya'? true:false;
		$pd->tempat_lahir = $request->input('tempat_lahir');
		$pd->tgl_lahir = $request->input('tgl_lahir');
		$pd->jenis_kelamin = $request->input('jenis_kelamin');
		$pd->agama = $request->input('agama');
		$pd->tinggi_badan = $request->input('tinggi_badan');
		$pd->berat_badan = $request->input('berat_badan');
		$pd->alamat_tinggal_sekarang = $request->input('alamat_tinggal_sekarang');
		$pd->kabupaten = $request->input('kabupaten');
		$pd->status_tempat_tinggal = $request->input('status_tempat_tinggal');
		$pd->no_call_rumah = $request->input('no_call_rumah');
		$pd->no_contact_person = $request->input('no_contact_person');
		$pd->tanggungan_pasangan = $request->input('tanggungan_pasangan') == 'ya'? true:false;
		$pd->nama_darurat = $request->input('nama_darurat');
		$pd->jumlah_anak = count($arr_anak);
		$pd->anak_ke = $request->input('anak_ke');
		$pd->jumlah_saudara = $request->input('jumlah_saudara');
		$pd->nama_darurat = $request->input('nama_darurat', null);
		$pd->hubungan_darurat = $request->input('hubungan_darurat', null);
		$pd->pekerjaan_darurat = $request->input('pekerjaan_darurat', null);
		$pd->alamat_darurat = $request->input('alamat_darurat', null);
		$pd->telp_darurat = $request->input('telp_darurat');
		$pd->ponsel_darurat = $request->input('ponsel_darurat');
		
		$pd->created_by = $request->user()->id;
		$pd->updated_by = $request->user()->id;
		if($pd->save())
		{
			
			//keluarga 1
			if($pd->status_menikah)
			{
				//save istri
				$pf = new PersonalFamily;
				$pf->personal_id = $pd->id;
				$pf->tipe = 'suami-istri';
				$pf->nama = $request->input('nama_pasangan');
				$pf->tempat_lahir = $request->input('tempat_lahir_pasangan');
				$pf->tgl_lahir = $request->input('tgl_lahir_pasangan');
				$pf->alamat = $request->input('alamat_pasangan');
				$pf->pekerjaan = $request->input('pekerjaan_pasangan');
				
				$pf->created_by = $request->user()->id;
				$pf->updated_by = $request->user()->id;
				$pf->save();
				
				//save anak
				if(!empty($arr_anak))
				{
					foreach($arr_anak as $anak)
					{
						$pf = new PersonalFamily;
						$pf->personal_id = $pd->id;
						$pf->tipe = 'anak';
						$pf->nama = $anak[0];
						$pf->tempat_lahir = $anak[1];
						$pf->tgl_lahir = $anak[2];
						
						$pf->created_by = $request->user()->id;
						$pf->updated_by = $request->user()->id;
						$pf->save();
					}
				}
			}
			
			//ayah
			$nama_ayah = $request->input('nama_ayah');
			if(!empty($nama_ayah) && is_array($nama_ayah))
			{
				$alamat_ayah = $request->input('alamat_ayah');
				$pekerjaan_ayah = $request->input('pekerjaan_ayah');
				
				$pf = new PersonalFamily;
				$pf->personal_id = $pd->id;
				$pf->tipe = 'ayah';
				$pf->nama = $nama_ayah[0];
				$pf->alamat = (!empty($alamat_ayah) && is_array($alamat_ayah))? $alamat_ayah[0]:null;
				$pf->pekerjaan = (!empty($pekerjaan_ayah) && is_array($pekerjaan_ayah))? $pekerjaan_ayah[0]:null;
				$pf->created_by = $request->user()->id;
				$pf->updated_by = $request->user()->id;
				$pf->save();
			}
			
			//ibu
			$nama_ibu = $request->input('nama_ibu');
			if(!empty($nama_ibu) && is_array($nama_ibu))
			{
				$alamat_ibu = $request->input('alamat_ibu');
				$pekerjaan_ibu = $request->input('pekerjaan_ibu');
				
				$pf = new PersonalFamily;
				$pf->personal_id = $pd->id;
				$pf->tipe = 'ibu';
				$pf->nama = $nama_ibu[0];
				$pf->alamat = (!empty($alamat_ibu) && is_array($alamat_ibu))? $alamat_ibu[0]:null;
				$pf->pekerjaan = (!empty($pekerjaan_ibu) && is_array($pekerjaan_ibu))? $pekerjaan_ibu[0]:null;
				$pf->created_by = $request->user()->id;
				$pf->updated_by = $request->user()->id;
				$pf->save();
			}
			
			//saudara
			$nama_saudara = $request->input('nama_saudara');
			if(!empty($nama_saudara) && is_array($nama_saudara))
			{
				$alamat_saudara = $request->input('alamat_saudara');
				$pekerjaan_saudara = $request->input('pekerjaan_saudara');
				
				for($i = 0; $i < count($nama_saudara); $i++)
				{
					$pf = new PersonalFamily;
					$pf->personal_id = $pd->id;
					$pf->tipe = 'adik-kakak';
					$pf->nama = $nama_saudara[$i];
					$pf->alamat = !empty($alamat_saudara[$i])? $alamat_saudara[$i]:null;
					$pf->pekerjaan = !empty($pekerjaan_saudara[$i])? $pekerjaan_saudara[$i]:null;
					$pf->created_by = $request->user()->id;
					$pf->updated_by = $request->user()->id;
					$pf->save();
				}
			}
			
			return response([
				'url' => '',
				'api_endpoint' => url('human_resource/'.$type),
				'api_method' => 'GET',
				'title' => $menu_title
			], 200);
		}
		
		return response('bad request', 400);
	}
	
	public function getHrProject(Request $request)
	{
		return $this->show($request, 'hr-project');
	}

	public function getHrStaf(Request $request)
	{
		return $this->show($request, 'hr-staf');
	}

	private function show(Request $request, $id)
	{
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $id)))
		{
			return response('Forbidden', 403);
		}
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $id));
		$menu_title = $menu->title;
		$menu_name = $menu->name;
		
		//param request
		$order_by = $request->input('ord', 'created_at');
		$sort_by = $request->input('srt', 'asc');
		$search['nama_lengkap'] = $request->input('nama_lengkap', '');
		$search['posisi'] = $request->input('posisi', '');
		$search['tgl_lahir'] = $request->input('tgl_lahir', '');
		$search['jenis_kelamin'] = $request->input('jenis_kelamin', '');
		$search['agama'] = $request->input('agama', '');
		$search['type'] = $id;
		$page = $request->input('page', 1);
		
		//religion & position
		$params = Param::where('type', '=', 'outsource-position')
			->orWhere('type', '=', 'staff-position')
			->orWhere('type', '=', 'religion')
			->orderBy('type')
			->orderBy('name')
			->get();
		
		$dropdown_religion = array();
		$dropdown_position = array();
		
		foreach($params	as $row)
		{
			if($row->type == 'religion')
			{
				$dropdown_religion[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'outsource-position' && $id == 'hr-project')
			{
				$dropdown_position[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'staff-position' && $id == 'hr-staf')
			{
				$dropdown_position[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
		}
		
		
		//search account code
		$personal_data = PersonalData::search($search)
			->joinAgama()
			->joinPosition();
		
		//get total page start {
		$limit = 10;
		$total_records = $personal_data->count();
		$total_pages = ceil($total_records/$limit);
		//get total page end }
		
		//get offset
		$offset = ($page-1) * $limit;
		
		//get privilege
		$has_edit = $request->user()->hasMenu($this->get_($this->menu_id, $id), 'put');
		$has_delete = $request->user()->hasMenu($this->get_($this->menu_id, $id), 'delete');
		
		//table header start{
		$table_header = array();
		$table_header[0][] = ['title' => 'No', 'width' => '8%'];
		$arr_width = ['24%', '23%', '15%', '15%', '15%'];
		
		if($has_edit && $has_delete)
		{
			$table_header[0][] = ['title' => '', 'width' => '8%'];
			$arr_width = ['20%', '20%', '15%', '14%', '15%'];
		}
		elseif($has_edit || $has_delete)
		{
			$table_header[0][] = ['title' => '', 'width' => '4%'];
			$arr_width = ['22%', '21%', '15%', '15%', '15%'];
		}
		
		$table_header[0][] = ['title' => 'Nama Lengkap', 'width' => $arr_width[0], 'id' => 'nama_lengkap', 'is_sort' => ($order_by == 'nama_lengkap'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[0][] = ['title' => 'Posisi', 'width' => $arr_width[1], 'id' => 'nama_posisi', 'is_sort' => ($order_by == 'nama_posisi'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[0][] = ['title' => 'Tgl Lahir', 'width' => $arr_width[2], 'id' => 'tgl_lahir', 'is_sort' => ($order_by == 'tgl_lahir'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[0][] = ['title' => 'Jkel', 'width' => $arr_width[3], 'id' => 'jenis_kelamin', 'is_sort' => ($order_by == 'jenis_kelamin'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[0][] = ['title' => 'Agama', 'width' => $arr_width[4], 'id' => 'agama', 'is_sort' => ($order_by == 'agama'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		//table header end }
		
		//get result start {
		$result = $personal_data
			->orderBy($order_by, $sort_by)
			->offset($offset)
			->take($limit)
			->get();
			
		$table_data = array();
		$pos = $offset;
		foreach($result as $row)
		{
			$pos++;
			
			if($has_edit || $has_delete)
			{
				$row_button = array(
					'type' => 'buttons',
					'list' => array()
				);
				
				if($has_edit)
				{
					$row_button['list'][] = [
						'text' => '<i class="fa fa-pencil"></i>', 
						'attr' => [
							'title' => 'Edit '.$menu_name,
							'data-title' => 'Edit '.$menu_title,
							'data-endpoint' => url("human_resource/edit-personal/{$row->id}?type={$id}"),
							'data-method' => 'GET'
						],
						'redirect' => [
							'url' => url('human_resource/'.$id),
							'param' => $request->all()
						]
					];
				}
				
				if($has_delete)
				{
					$row_button['list'][] = [
						'text' => '<i class="glyphicon glyphicon-remove"></i>', 
						'attr' => [
							'title' => 'Delete '.$menu_name,
							'data-title' => 'Delete '.$menu_title,
							'data-endpoint' => url("human_resource/edit-personal/{$row->id}?type={$id}"),
							'data-method' => 'delete',
							'data-token' => csrf_token()
						]
					];
				}
				
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					$row_button,
					['type' => 'text', 'text' => $row->nama_lengkap],
					['type' => 'text', 'text' => $row->nama_posisi],
					['type' => 'text', 'text' => $row->str_tgl_lahir],
					['type' => 'text', 'text' => ucfirst($row->jenis_kelamin)],
					['type' => 'text', 'text' => $row->nama_agama],
				];
			}
			else
			{
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					['type' => 'text', 'text' => $row->kode],
					['type' => 'text', 'text' => $row->uraian_rekening],
				];
			}
		}
		//get result end }
		
		//return result
		
		//type
		$return_result['type'] = 'table list';
		
		//form attribute
		$return_result['form']['attr'] = [
			'method' => 'get',
			'action' => url('/human_resource/'.$id),
			'role' => 'form',
			'data-type' => 'table list',
			'data-title' => $menu_title,
			'data-endpoint' => url('/human_resource/'.$id),
			'data-method' => 'GET'
		];
		
		//form hidden elements
		$return_result['form']['hidden'] = [
			['name' => 'page', 'value' => $page],
			['name' => 'ord', 'value' => $order_by],
			['name' => 'srt', 'value' => $sort_by],
		];
		
		//form visible elements
		$return_result['form']['elements'] = array();
		
		//block 1
		$return_result['form']['elements'][0][0] = [
			[
				'label' => 'Nama Lengkap', 
				'element' => 'input',
				'attr' => [
					'type' => 'text', 
					'name' => 'kode',
				]
			],
			[
				'label' => 'Jenis Kelamin', 
				'element' => 'select',
				'attr' => [
					'name' => 'jenis_kelamin',
				],
				'options' => [
					[
						'attr' => ['value' => 'pria'],
						'html' => 'Pria'
					],
					[
						'attr' => ['value' => 'wanita'],
						'html' => 'Wanita'
					]
				]
			],
			[
				'label' => 'Tanggal Lahir', 
				'element' => 'datepicker_range',
				'attr' => [
					'name' => 'tgl_lahir',
				]
			]
		];
		
		//block 2
		$return_result['form']['elements'][0][1] = [
			[
				'label' => 'Posisi', 
				'element' => 'select',
				'attr' => [
					'name' => 'posisi',
				],
				'options' => $dropdown_position
			],
			[
				'label' => 'Agama', 
				'element' => 'select',
				'attr' => [
					'name' => 'agama',
				],
				'options' => $dropdown_religion
			]
		];
		
		//block 3 search
		$return_result['form']['elements'][1][0] = [
			[
				'label' => '<span class="glyphicon glyphicon-search"></span> Search', 
				'element' => 'button',
				'attr' => [
					'type' => 'submit', 
					'class' => 'btn btn-primary',
					'style' => 'margin-right:5px'
				]
			],
			[
				'label' => 'Reset', 
				'element' => 'button',
				'attr' => [
					'type' => 'reset', 
					'class' => 'btn btn-warning',
					'style' => 'margin-right:5px'
				]
			],
		];
		
		//form data
		$return_result['form_data'] = [
			'nama_lengkap' => $search['nama_lengkap'],
			'posisi' => $search['posisi'],
			'tgl_lahir' => $search['tgl_lahir'],
			'jenis_kelamin' => $search['jenis_kelamin'],
			'agama' => $search['agama'],
		];
			
		$return_result['total_records'] = $total_records;
		$return_result['total_page'] = $total_pages;
		$return_result['table_header'] = $table_header;
		$return_result['table_data'] = $table_data;
		
		//punya akses post, kasih button input user
		if($request->user()->hasMenu($this->get_($this->menu_id, $id), 'post'))
		{
			$return_result['form']['elements'][1][0][] = [
				'label' => '<span class="glyphicon glyphicon-plus"></span> Tambah '.$menu_name, 
				'element' => 'button',
				'attr' => [
					'type' => 'button',
					'class' => 'btn btn-success',
					'data-title' => 'Input '.$menu_title,
					'data-endpoint' => url('human_resource/personal?type='.$id),
					'data-method' => 'get'
				],
				'redirect' => [
					'url' => url('human_resource/'.$id),
					'param' => $request->all()
				]
			];
		}
		
		return response($return_result, 200);
	}
}
