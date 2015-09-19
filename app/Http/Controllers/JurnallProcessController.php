<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\AccountCode;
use App\Models\AccountCodeType;
use App\Models\JurnallProcess;
use App\Models\Menu;
use DB;

class JurnallProcessController extends Controller
{
	//id user di dalam table menu
	private $menu_id = 8;
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		//check privilege
		if(!$request->user()->hasMenu($this->menu_id))
		{
			return response('Forbidden', 403);
		}
		$has_edit = $request->user()->hasMenu($this->menu_id, 'put');
		$has_delete = $request->user()->hasMenu($this->menu_id, 'delete');
		
		//menu title
		$menu = Menu::findOrFail($this->menu_id);
		$menu_title = $menu->title;
		
		//param request
		$order_by = $request->input('ord', 'tanggal');
		$sort_by = $request->input('srt', 'desc');
		$tanggal = $request->input('created_at', ['', '']);
		$page = $request->input('page', 1);
		
		//search data
		$jurnall = JurnallProcess::search(array('tanggal' => $tanggal));
		
		//get total page start {
		$limit = 10;
		$total_records = $jurnall->count();
		$total_pages = ceil($total_records/$limit);
		//get total page end }
		
		//get offset
		$offset = ($page-1) * $limit;
		
		//table header start{
		$table_header = array();
		$table_header[] = ['title' => 'Tanggal', 'id' => 'tanggal', 'is_sort' => ($order_by == 'tanggal'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		
		if($has_edit || $has_delete)
		{
			$table_header[] = ['title' => '',];
		}
		
		$table_header[] = ['title' => 'Kode', 'id' => 'kode', 'is_sort' => ($order_by == 'kode'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[] = ['title' => 'Rekening', 'id' => 'rekening', 'is_sort' => ($order_by == 'rekening'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[] = ['title' => 'Uraian', 'id' => 'uraian', 'is_sort' => ($order_by == 'uraian'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[] = ['title' => 'Debet', 'id' => 'debet', 'is_sort' => ($order_by == 'debet'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[] = ['title' => 'Kredit', 'id' => 'kredit', 'is_sort' => ($order_by == 'kredit'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		//table header end }
		
		
		//get result start {
		$jurnall
			->select(
				'*',
				DB::raw("to_char(tanggal, 'DD-Mon-YYYY') as str_tanggal")
			)
			->offset($offset)
			->take($limit);
		
		if($order_by == 'kode')
		{
			$result = $jurnall
				->with(['account_code' => function($query) use($order_by, $sort_by){
					$query->orderBy($order_by, $sort_by);
				}])
				->get();
		}
		else
		{
			$result = $jurnall
				->orderBy($order_by, $sort_by)
				->get();
		}
		
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
							'title' => 'Edit '.$menu_title,
							'data-title' => 'Edit '.$menu_title,
							'data-endpoint' => url("jurnall_process/{$row->id}/edit"),
							'data-method' => 'GET'
						],
						'redirect' => [
							'url' => url('jurnall_process'),
							'param' => $request->all()
						]
					];
				}
				
				if($has_delete)
				{
					$row_button['list'][] = [
						'text' => '<i class="glyphicon glyphicon-remove"></i>', 
						'attr' => [
							'title' => 'Delete '.$menu_title,
							'data-title' => 'Delete '.$menu_title,
							'data-endpoint' => url("jurnall_process/{$row->id}"),
							'data-method' => 'delete',
							'data-token' => csrf_token()
						]
					];
				}
				
				$table_data[] = [
					['type' => 'text', 'text' => $row->str_tanggal],
					$row_button,
					['type' => 'text', 'text' => $row->account_code->kode],
					['type' => 'text', 'text' => $row->rekening],
					['type' => 'text', 'text' => $row->uraian],
					['type' => 'text', 'text' => number_format($row->debet)],
					['type' => 'text', 'text' => number_format($row->kredit)],
				];
			}
			else
			{
				$table_data[] = [
					['type' => 'text', 'text' => $row->str_tanggal],
					['type' => 'text', 'text' => $row->account_code->kode],
					['type' => 'text', 'text' => $row->rekening],
					['type' => 'text', 'text' => $row->uraian],
					['type' => 'text', 'text' => number_format($row->debet)],
					['type' => 'text', 'text' => number_format($row->kredit)],
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
					'action' => url('jurnall_process'),
					'role' => 'form',
					'data-type' => 'table list',
					'data-title' => $menu_title,
					'data-endpoint' => url('jurnall_process'),
					'data-method' => 'GET'
				],
			
				'hidden' => [
					['name' => 'page', 'value' => 1],
					['name' => 'ord', 'value' => $order_by],
					['name' => 'srt', 'value' => $sort_by],
				],
				
				'elements_blok' => [
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Tanggal', 
								'element' => 'datepicker_range',
								'attr' => [
									'name' => 'tanggal',
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
				'tanggal' => [$tanggal[0], $tanggal[1]]
			],
			
			'total_records' => $total_records,
			'total_page' => $total_pages,
			
			'table_header' => $table_header,
			
			'table_data' => $table_data
		];
		
		//punya akses post, kasih button input user
		if($request->user()->hasMenu($this->menu_id, 'post'))
		{
			$return_result['form']['elements_blok'][2]['fields'][] = [
				'label' => '<span class="glyphicon glyphicon-plus"></span> Tambah '.$menu_title, 
				'element' => 'button',
				'attr' => [
					'type' => 'button',
					'class' => 'btn btn-success',
					'data-title' => 'Input '.$menu_title,
					'data-endpoint' => url('jurnall_process/create'),
					'data-method' => 'get'
				],
				'redirect' => [
					'url' => url('jurnall_process'),
					'param' => $request->all()
				]
			];
		}
		
		return response($return_result, 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Request $request)
	{
		$redirect_url = $request->input('redirect');
		
		if(!$request->user()->hasMenu($this->menu_id, 'post'))
		{
			return response('forbidden', 403);
		}
		
		//menu title
		$menu = Menu::findOrFail($this->menu_id);
		$menu_title = $menu->title;
		$menu_name = $menu->name;
		
		//drop down account code
		$drop_down = AccountCodeType::dropDown();
		
		$return_result = [
			'type' => 'input form',
			'form' => [
				'attr' => [
					'method' => 'post',
					'action' => url('jurnall_process'),
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
								'label' => 'Tanggal', 
								'element' => 'datepicker',
								'attr' => [
									'type' => 'text', 
									'name' => 'tanggal',
								]
							],
							[
								'label' => 'Kode', 
								'element' => 'select',
								'attr' => [
									'type' => 'text', 
									'name' => 'account_code_id',
								],
								'optgroup' => $drop_down
							],
							[
								'label' => 'Rekening', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'rekening',
									'maxlength' => 30
								]
							],
						]
					],
					
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Uraian', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'uraian',
									'maxlength' => 255
								]
							],
							[
								'label' => 'Debet', 
								'element' => 'number_format',
								'attr' => [
									'type' => 'text', 
									'name' => 'debet',
								]
							],
							[
								'label' => 'Kredit', 
								'element' => 'number_format',
								'attr' => [
									'type' => 'text', 
									'name' => 'kredit',
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
		//check privilege
		if(!$request->user()->hasMenu($this->menu_id, 'post'))
		{
			return response('Forbidden', 403);
		}
		
		//validate
		$this->validate(
			$request, 
			[
				'tanggal' => 'required|date_format:Y-m-d',
				'account_code_id' => 'required|exists:account_codes,id',
				'rekening' => 'required|max:30',
				'uraian' => 'required|max:255',
				'debet' => 'required|numeric',
				'kredit' => 'required|numeric',
			],
			[
				'tanggal.required' => '<span style="font-weight:bold;font-style:italic">Tanggal</span> harap di isi',
				'tanggal.date_format' => '<span style="font-weight:bold;font-style:italic">Tanggal</span> tidak valid',
				
				'account_code_id.required' => '<span style="font-weight:bold;font-style:italic">Kode</span> harap di isi',
				'account_code_id.required' => '<span style="font-weight:bold;font-style:italic">Kode</span> tidak valid',
				'rekening.required' => '<span style="font-weight:bold;font-style:italic">Rekening</span> harap di isi',
				'uraian.required' => '<span style="font-weight:bold;font-style:italic">Uraian</span> harap di isi',
				'debet.required' => '<span style="font-weight:bold;font-style:italic">Debet</span> harap di isi',
				'kredit.required' => '<span style="font-weight:bold;font-style:italic">Kredit</span> harap di isi',
			]
		);
		
		//menu title
		$menu = Menu::findOrFail($this->menu_id);
		$menu_title = $menu->title;
		
		//save
		$jurnall = new JurnallProcess;
		$jurnall->account_code_id = $request->input('account_code_id');
		$jurnall->rekening = $request->input('rekening');
		$jurnall->uraian = $request->input('uraian');
		$jurnall->debet = $request->input('debet');
		$jurnall->kredit = $request->input('kredit');
		$jurnall->tanggal = $request->input('tanggal');
		$jurnall->created_by = $request->user()->id;
		$jurnall->updated_by = $request->user()->id;
		if($jurnall->save())
		{
			return response([
				'url' => '',
				'api_endpoint' => url('jurnall_process'),
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
	public function show($id)
	{
		//
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
}
