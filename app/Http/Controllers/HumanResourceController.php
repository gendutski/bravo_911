<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Param;
use App\Models\PersonalData;
use DB;

class HumanResourceController extends Controller
{
	private $menu_id = array(
		'hr_project' => 28,
		'hr_staf' => 29,
	);
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return response('not found', 404);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
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
		
		$dropdown_religion = array();
		$dropdown_position = array();
		$dropdown_tribe = array();
		$dropdown_location = array();
		$dropdown_disease = array();
		$dropdown_formal_education = array();
		$dropdown_training = array();
		$dropdown_language = array();
		$dropdown_district = array();
		$dropdown_status_of_residence = array();
		
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
			elseif($row->type == 'tribe')
			{
				$dropdown_tribe[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'location')
			{
				$dropdown_location[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'disease')
			{
				$dropdown_disease[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'formal-education')
			{
				$dropdown_formal_education[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'training')
			{
				$dropdown_training[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'language')
			{
				$dropdown_language[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'district')
			{
				$dropdown_district[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'status-of-residence')
			{
				$dropdown_status_of_residence[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'outsource-position' && $id == 'hr_project')
			{
				$dropdown_position[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'staff-position' && $id == 'hr_staf')
			{
				$dropdown_position[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
		}
		
		$return_result = [
			'type' => 'input form',
			'form' => [
				'attr' => [
					'method' => 'post',
					'action' => url('human_resource?type='.$type),
					'role' => 'form'
				],
				
				'hidden' => [
					['name' => '_token', 'value' => csrf_token()],
					['name' => '_redirect', 'value' => $redirect_url]
				],
				
				'elements_blok' => [
					//kolom 1
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Posisi Yang Dilamar', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'uraian_rekening',
									'maxlength' => 255
								]
							],
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
								'options' => $dropdown_location
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
								'options' => $dropdown_tribe
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
										'attr' => ['value' => 0],
										'html' => 'Belum Menikah'
									],
									[
										'attr' => ['value' => 1],
										'html' => 'Menikah'
									],
								]
							],
						]
					],
					
					//kolom 2
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Tempat Lahir', 
								'element' => 'select',
								'attr' => [
									'name' => 'tempat_lahir',
								],
								'options' => $dropdown_location
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
								'options' => $dropdown_religion
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
								'options' => $dropdown_district
							],
							[
								'label' => 'Status Tmpt Tinggal', 
								'element' => 'select',
								'attr' => [
									'name' => 'status_tempat_tinggal',
								],
								'options' => $dropdown_status_of_residence
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
						]
					],
					
					[
						'css_class' => 'clearfix visible-lg-block',
						'fields' => []
					],
					
					[
						'css_class' => 'col-lg-6',
						'fields' => [
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
						]
					],
				]
			],
			'form_data' => []
		];
		
		return response($return_result, 200);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show(Request $request, $id)
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
			elseif($row->type == 'outsource-position' && $id == 'hr_project')
			{
				$dropdown_position[] = array(
					'attr' => [
						'value' => $row->id
					],
					'html' => $row->name
				);
			}
			elseif($row->type == 'staff-position' && $id == 'hr_staf')
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
							'data-endpoint' => url("human_resource/{$row->id}/edit?type={$id}"),
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
							'data-endpoint' => url("human_resource/{$row->id}?type={$id}"),
							'data-method' => 'delete',
							'data-token' => csrf_token()
						]
					];
				}
				
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					$row_button,
					['type' => 'text', 'text' => $row->kode],
					['type' => 'text', 'text' => $row->uraian_rekening],
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
		$return_result = [
			'type' => 'table list',
			'form' => [
				'attr' => [
					'method' => 'get',
					'action' => url('/human_resource/'.$id),
					'role' => 'form',
					'data-type' => 'table list',
					'data-title' => $menu_title,
					'data-endpoint' => url('/human_resource/'.$id),
					'data-method' => 'GET'
				],
			
				'hidden' => [
					['name' => 'page', 'value' => $page],
					['name' => 'ord', 'value' => $order_by],
					['name' => 'srt', 'value' => $sort_by],
				],
				
				'elements_blok' => [
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Nama Lengkap', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'kode',
								]
							],
						]
					],
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Posisi', 
								'element' => 'select',
								'attr' => [
									'name' => 'posisi',
								],
								'options' => $dropdown_position
							],
						]
					],
					[
						'css_class' => 'clearfix visible-lg-block',
						'fields' => []
					],
					[
						'css_class' => 'col-lg-6',
						'fields' => [
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
						]
					],
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Agama', 
								'element' => 'select',
								'attr' => [
									'name' => 'agama',
								],
								'options' => $dropdown_religion
							],
						]
					],
					[
						'css_class' => 'clearfix visible-lg-block',
						'fields' => []
					],
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Tanggal Lahir', 
								'element' => 'datepicker_range',
								'attr' => [
									'name' => 'tgl_lahir',
								]
							],
						]
					],
					[
						'css_class' => 'clearfix visible-lg-block',
						'fields' => []
					],
					[
						'css_class' => 'col-lg-6',
						'fields' => [
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
						]
					],
				]
			],
			'form_data' => [
				'nama_lengkap' => $search['nama_lengkap'],
				'posisi' => $search['posisi'],
				'tgl_lahir' => $search['tgl_lahir'],
				'jenis_kelamin' => $search['jenis_kelamin'],
				'agama' => $search['agama'],
			],
			
			'total_records' => $total_records,
			'total_page' => $total_pages,
			
			'table_header' => $table_header,
			
			'table_data' => $table_data
		];
		
		//punya akses post, kasih button input user
		if($request->user()->hasMenu($this->get_($this->menu_id, $id), 'post'))
		{
			$return_result['form']['elements_blok'][8]['fields'][] = [
				'label' => '<span class="glyphicon glyphicon-plus"></span> Tambah '.$menu_name, 
				'element' => 'button',
				'attr' => [
					'type' => 'button',
					'class' => 'btn btn-success',
					'data-title' => 'Input '.$menu_title,
					'data-endpoint' => url('human_resource/create?type='.$id),
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

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  Request  $request
	 * @param  int  $id
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	
	//cek menu_id dan account_type_id
	private function get_($key, $id)
	{
		if(array_key_exists($id, $key))
		{
			return $key[$id];
		}
		return 0;
	}
}
