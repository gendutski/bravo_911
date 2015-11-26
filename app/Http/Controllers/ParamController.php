<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Param;
use DB;

class ParamController extends Controller
{
	private $menu_id = array(
		'outsource-position' => 31,
		'staff-position' => 32,
		'religion' => 33,
		'tribe' => 34,
		'location' => 35,
		'district' => 36,
		'disease' => 37,
		'formal-education' => 38,
		'training' => 39,
		'language' => 40,
		'status-of-residence' => 41,
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
		
		$return_result = [
			'type' => 'input form',
			'form' => [
				'attr' => [
					'method' => 'post',
					'action' => url('param?type='.$type),
					'role' => 'form'
				],
			
				'hidden' => [
					['name' => '_token', 'value' => csrf_token()],
					['name' => '_redirect', 'value' => $redirect_url]
				],
				
				'elements_blok' => [
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Nama', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'name',
									'maxlength' => 100
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
				'name' => 'required|max:100|unique:params,name,NULL,id,type,'.$type,
			],
			[
				'name.required' => '<span style="font-weight:bold;font-style:italic">Nama</span> harap di isi',
				'name.unique' => 'Nama <span style="font-weight:bold;font-style:italic">'.$request->input('name').'</span> sudah terdaftar. Harap pilih nama yang lain!',
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		
		//save
		$params = new Param;
		$params->name = $request->input('name');
		$params->type = $type;
		$params->created_by = $request->user()->id;
		$params->updated_by = $request->user()->id;
		if($params->save())
		{
			return response([
				'url' => '',
				'api_endpoint' => url('param/'.$type),
				'api_method' => 'GET',
				'title' => $menu_title
			], 200);
		}
		
		return response('bad request', 400);
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
		$search['name'] = $request->input('name', '');
		$search['type'] = $id;
		$page = $request->input('page', 1);
		
		//search params table
		$params = Param::search($search);
		
		//get total page start {
		$limit = 10;
		$total_records = $params->count();
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
		$arr_width = ['92%'];
		
		if($has_edit && $has_delete)
		{
			$table_header[0][] = ['title' => '', 'width' => '8%'];
			$arr_width = ['84%'];
		}
		elseif($has_edit || $has_delete)
		{
			$table_header[0][] = ['title' => '', 'width' => '4%'];
			$arr_width = ['88%'];
		}
		
		$table_header[0][] = ['title' => 'Nama', 'width' => $arr_width[0], 'id' => 'name', 'is_sort' => ($order_by == 'name'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		//table header end }
		
		//get result start {
		$result = $params
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
							'data-endpoint' => url("param/{$row->id}/edit?type={$id}"),
							'data-method' => 'GET'
						],
						'redirect' => [
							'url' => url('param/'.$id),
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
							'data-endpoint' => url("param/{$row->id}?type={$id}"),
							'data-method' => 'delete',
							'data-token' => csrf_token()
						]
					];
				}
				
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					$row_button,
					['type' => 'text', 'text' => $row->name],
				];
			}
			else
			{
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					['type' => 'text', 'text' => $row->name]
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
					'action' => url('/param/'.$id),
					'role' => 'form',
					'data-type' => 'table list',
					'data-title' => $menu_title,
					'data-endpoint' => url('/param/'.$id),
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
								'label' => 'Nama', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'name',
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
				'name' => $search['name'],
			],
			
			'total_records' => $total_records,
			'total_page' => $total_pages,
			
			'table_header' => $table_header,
			
			'table_data' => $table_data
		];
		
		//punya akses post, kasih button input user
		if($request->user()->hasMenu($this->get_($this->menu_id, $id), 'post'))
		{
			$return_result['form']['elements_blok'][2]['fields'][] = [
				'label' => '<span class="glyphicon glyphicon-plus"></span> Tambah '.$menu_name, 
				'element' => 'button',
				'attr' => [
					'type' => 'button',
					'class' => 'btn btn-success',
					'data-title' => 'Input '.$menu_title,
					'data-endpoint' => url('param/create?type='.$id),
					'data-method' => 'get'
				],
				'redirect' => [
					'url' => url('param/'.$id),
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
	public function edit(Request $request, $id)
	{
		//request
		$type = $request->input('type');
		$redirect_url = $request->input('redirect');
		
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'put'))
		{
			return response('Forbidden', 403);
		}
		
		$params = Param::findOrFail($id);
		if($params->type != $type){ return response('not found', 404); }
		
		$return_result = $this->create($request)->original;
		
		//set action
		$return_result['form']['attr']['action'] = url('param/'.$id.'?type='.$type);
		
		//set method
		$return_result['form']['attr']['method'] = 'put';
		
		$return_result['form_data'] = [
			'name' => $params->name,
		];
		
		return response($return_result, 200);
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
		//request
		$type = $request->input('type');
		$redirect_url = $request->input('redirect');
		
		//privilege
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'put'))
		{
			return response('Forbidden', 403);
		}
		
		//validate
		$this->validate(
			$request, 
			[
				'name' => 'required|max:100|unique:params,name,'.$id.',id,type,'.$type,
			],
			[
				'name.required' => '<span style="font-weight:bold;font-style:italic">Nama</span> harap di isi',
				'name.unique' => 'Nama <span style="font-weight:bold;font-style:italic">'.$request->input('name').'</span> sudah terdaftar. Harap pilih kode yang lain!',
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		
		$redirect_endpoint = $request->input('_redirect', url('param/'.$type));
		
		//get data
		$params = Param::findOrFail($id);
		
		//save data
		$params->name = $request->input('name');
		$params->updated_by = $request->user()->id;
		
		if($params->save())
		{
			return response([
				'url' => '',
				'api_endpoint' => $redirect_endpoint,
				'api_method' => 'GET',
				'title' => $menu_title
			], 200);
		}
		
		return response('', 500);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
	{
		//request
		$type = $request->input('type');
		
		if(!$request->user()->hasMenu($this->get_($this->menu_id, $type), 'delete'))
		{
			return response('Forbidden', 403);
		}
		
		if(is_numeric($id))
		{
			$params = Param::findOrFail($id);
			if($params->delete())
			{
				return response(['result' => true], 200);
			}
			return response('server error', 500);
		}
		return response('bad request', 400);
	}
	
	
	//cek menu_id
	private function get_($key, $id)
	{
		if(array_key_exists($id, $key))
		{
			return $key[$id];
		}
		return 0;
	}
}
