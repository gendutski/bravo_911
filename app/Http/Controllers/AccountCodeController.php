<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AccountCode;
use App\Models\AccountCodeType;
use App\Models\Menu;
use DB;

class AccountCodeController extends Controller
{
	private $menu_id = array(
		'aktiva' => 23,
		'pasiva' => 24,
		'modal' => 25,
		'pendapatan' => 26,
		'biaya' => 27
	);
	
	private $account_type_id = array(
		'aktiva' => 1,
		'pasiva' => 2,
		'modal' => 3,
		'pendapatan' => 4,
		'biaya' => 5
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
		
		//type
		$return_result['type'] = 'input form';
		
		//form attribute
		$return_result['form']['attr'] = [
			'method' => 'post',
			'action' => url('account_code?type='.$type),
			'role' => 'form'
		];
		
		//form hidden elements
		$return_result['form']['hidden'] = [
			['name' => '_token', 'value' => csrf_token()],
			['name' => '_redirect', 'value' => $redirect_url]
		];
		
		//form visible elements
		$return_result['form']['elements'] = array();
		
		//block 1 {
		$return_result['form']['elements'][0][0][] = [
			'label' => 'Kode', 
			'element' => 'input',
			'attr' => [
				'type' => 'text', 
				'name' => 'kode',
				'maxlength' => 30
			]
		];
		
		$return_result['form']['elements'][0][0][] = [
			'label' => 'Uraian Rekening', 
			'element' => 'input',
			'attr' => [
				'type' => 'text', 
				'name' => 'uraian_rekening',
				'maxlength' => 255
			]
		];
		//block 1 }
		
		//void block
		$return_result['form']['elements'][0][1] = array();
		
		$return_result['form']['elements'][1][0][] = [
			'label' => 'Submit', 
			'element' => 'button',
			'attr' => [
				'type' => 'submit', 
				'class' => 'btn btn-primary',
				'style' => 'margin-right:5px'
			]
		];
		
		$return_result['form']['elements'][1][0][] = [
			'label' => 'Reset', 
			'element' => 'button',
			'attr' => [
				'type' => 'reset', 
				'class' => 'btn btn-warning',
				'style' => 'margin-right:5px'
			]
		];
		$return_result['form']['elements'][1][0][] = [
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
		];
		
		$return_result['form_data'] = [];
		
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
				'kode' => 'required|max:30|unique:account_codes,kode,NULL,id,type_id,'.$this->get_($this->account_type_id, $type),
				'uraian_rekening' => 'required|max:255',
			],
			[
				'kode.required' => '<span style="font-weight:bold;font-style:italic">Kode</span> harap di isi',
				'kode.unique' => 'Kode <span style="font-weight:bold;font-style:italic">'.$request->input('kode').'</span> sudah terdaftar. Harap pilih kode yang lain!',
				'uraian_rekening.required' => '<span style="font-weight:bold;font-style:italic">Uraian Rekening</span> harap di isi',
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		
		//save
		$ac = new AccountCode;
		$ac->kode = $request->input('kode');
		$ac->type_id = $this->get_($this->account_type_id, $type);
		$ac->uraian_rekening = $request->input('uraian_rekening');
		$ac->created_by = $request->user()->id;
		$ac->updated_by = $request->user()->id;
		if($ac->save())
		{
			return response([
				'url' => '',
				'api_endpoint' => url('account_code/'.$type),
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
		
		//account code type
		$ac_type = AccountCodeType::findOrFail($this->get_($this->account_type_id, $id));
		
		//param request
		$order_by = $request->input('ord', 'created_at');
		$sort_by = $request->input('srt', 'asc');
		$search['kode'] = $request->input('kode', '');
		$search['uraian_rekening'] = $request->input('uraian_rekening', '');
		$page = $request->input('page', 1);
		
		//search account code
		$ac = $ac_type->account_code()->search($search);
		
		//get total page start {
		$limit = 10;
		$total_records = $ac->count();
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
		$arr_width = ['32%', '60%'];
		
		if($has_edit && $has_delete)
		{
			$table_header[0][] = ['title' => '', 'width' => '8%'];
			$arr_width = ['32%', '52%'];
		}
		elseif($has_edit || $has_delete)
		{
			$table_header[0][] = ['title' => '', 'width' => '4%'];
			$arr_width = ['32%', '56%'];
		}
		
		$table_header[0][] = ['title' => 'Kode', 'width' => $arr_width[0], 'id' => 'kode', 'is_sort' => ($order_by == 'kode'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[0][] = ['title' => 'Uraian Rekening', 'width' => $arr_width[1], 'id' => 'uraian_rekening', 'is_sort' => ($order_by == 'uraian_rekening'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		//table header end }
		
		//get result start {
		$result = $ac
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
							'data-endpoint' => url("account_code/{$row->id}/edit?type={$id}"),
							'data-method' => 'GET'
						],
						'redirect' => [
							'url' => url('account_code/'.$id),
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
							'data-endpoint' => url("account_code/{$row->id}?type={$id}"),
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
		
		//type
		$return_result['type'] = 'table list';
		
		//form attribute
		$return_result['form']['attr'] = [
			'method' => 'get',
			'action' => url('/account_code/'.$id),
			'role' => 'form',
			'data-type' => 'table list',
			'data-title' => $menu_title,
			'data-endpoint' => url('/account_code/'.$id),
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
		
		$return_result['form']['elements'][0][0][] = [
			'label' => 'Kode', 
			'element' => 'input',
			'attr' => [
				'type' => 'text', 
				'name' => 'kode',
			]
		];
		
		$return_result['form']['elements'][0][1][] = [
			'label' => 'Uraian Rekening', 
			'element' => 'input',
			'attr' => [
				'type' => 'text',
				'name' => 'uraian_rekening',
			]
		];
		
		//submit search
		$return_result['form']['elements'][1][0][] = [
			'label' => '<span class="glyphicon glyphicon-search"></span> Search', 
			'element' => 'button',
			'attr' => [
				'type' => 'submit', 
				'class' => 'btn btn-primary',
				'style' => 'margin-right:5px'
			]
		];
		
		//reset search
		$return_result['form']['elements'][1][0][] = [
			'label' => 'Reset', 
			'element' => 'button',
			'attr' => [
				'type' => 'reset', 
				'class' => 'btn btn-warning',
				'style' => 'margin-right:5px'
			]
		];
		
		//form data
		$return_result['form_data'] = [
			'kode' => $search['kode'],
			'uraian_rekening' => $search['uraian_rekening'],
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
					'data-endpoint' => url('account_code/create?type='.$id),
					'data-method' => 'get'
				],
				'redirect' => [
					'url' => url('account_code/'.$id),
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
		
		$ac = AccountCode::findOrFail($id);
		if($ac->type_id != $this->get_($this->account_type_id, $type)){ return response('not found', 404); }
		
		$return_result = $this->create($request)->original;
		
		//set action
		$return_result['form']['attr']['action'] = url('account_code/'.$id.'?type='.$type);
		
		//set method
		$return_result['form']['attr']['method'] = 'put';
		
		$return_result['form_data'] = [
			'kode' => $ac->kode,
			'uraian_rekening' => $ac->uraian_rekening
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
				'kode' => 'required|max:30|unique:account_codes,kode,'.$id.',id,type_id,'.$this->get_($this->account_type_id, $type),
				'uraian_rekening' => 'required|max:255',
			],
			[
				'kode.required' => '<span style="font-weight:bold;font-style:italic">Kode</span> harap di isi',
				'kode.unique' => 'Kode <span style="font-weight:bold;font-style:italic">'.$request->input('kode').'</span> sudah terdaftar. Harap pilih kode yang lain!',
				'uraian_rekening.required' => '<span style="font-weight:bold;font-style:italic">Uraian Rekening</span> harap di isi',
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->get_($this->menu_id, $type));
		$menu_title = $menu->title;
		
		$redirect_endpoint = $request->input('_redirect', url('account_code/'.$type));
		
		//get data
		$user = AccountCode::findOrFail($id);
		
		//save data
		$user->kode = $request->input('kode');
		$user->uraian_rekening = $request->input('uraian_rekening');
		$user->updated_by = $request->user()->id;
		
		if($user->save())
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
			$ac = AccountCode::findOrFail($id);
			if($ac->delete())
			{
				return response(['result' => true], 200);
			}
			return response('server error', 500);
		}
		return response('bad request', 400);
	}
}
