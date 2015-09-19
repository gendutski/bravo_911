<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\User;

use DB;

class UserController extends Controller
{
	//id user di dalam table menu
	private $menu_id = 21;
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		//get privilege
		$user_id = $request->user()->id;
		$privilege = User::find($user_id)->hasMenu($this->menu_id);
		if(empty($privilege)){return response('forbidden', 403);}
		$has_edit = User::find($user_id)->hasMenu($this->menu_id, 'put');
		$has_delete = User::find($user_id)->hasMenu($this->menu_id, 'delete');
		
		
		//param request
		$order_by = $request->input('ord', 'created_at');
		$sort_by = $request->input('srt', 'asc');
		$name_or_email = $request->input('name_or_email', '');
		$created_at = $request->input('created_at', ['', '']);
		$page = $request->input('page', 1);
		
		//search user
		$user = User::search(array('name_or_email' => $name_or_email, 'created_at' => $created_at));
		
		//get total page start {
		$limit = 10;
		$total_records = $user->count();
		$total_pages = ceil($total_records/$limit);
		//get total page end }
		
		//get offset
		$offset = ($page-1) * $limit;
		
		//table header start{
		$table_header = array();
		$table_header[] = ['title' => 'No', 'width' => '8%'];
		$arr_width = ['36%', '36%'];
		
		if($has_edit && $has_delete)
		{
			$table_header[] = ['title' => '', 'width' => '8%'];
			$arr_width = ['32%', '32%'];
		}
		elseif($has_edit || $has_delete)
		{
			$table_header[] = ['title' => '', 'width' => '4%'];
			$arr_width = ['34%', '34%'];
		}
		
		$table_header[] = ['title' => 'Nama Lengkap', 'width' => $arr_width[0], 'id' => 'name', 'is_sort' => ($order_by == 'name'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[] = ['title' => 'Email', 'width' => $arr_width[1], 'id' => 'email', 'is_sort' => ($order_by == 'email'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		$table_header[] = ['title' => 'Created', 'width' => '20%', 'id' => 'created_at', 'is_sort' => ($order_by == 'created_at'? ($sort_by == 'desc'? 'desc':'asc'):'')];
		//table header end }
		
		
		//get result start {
		$result = $user
			->select(
				'*',
				DB::raw("to_char(created_at, 'DD-Mon-YYYY HH24:MI:SS') as str_created_at")
			)
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
							'title' => 'Edit User',
							'data-title' => 'Edit User',
							'data-endpoint' => url("user/{$row->id}/edit"),
							'data-method' => 'GET'
						],
						'redirect' => [
							'url' => url('user'),
							'param' => $request->all()
						]
					];
				}
				
				if($has_delete)
				{
					$row_button['list'][] = [
						'text' => '<i class="glyphicon glyphicon-remove"></i>', 
						'attr' => [
							'title' => 'Delete User',
							'data-title' => 'Delete User',
							'data-endpoint' => url("user/{$row->id}"),
							'data-method' => 'delete',
							'data-token' => csrf_token()
						]
					];
				}
				
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					$row_button,
					['type' => 'text', 'text' => $row->name],
					['type' => 'text', 'text' => $row->email],
					['type' => 'text', 'text' => $row->str_created_at]
				];
			}
			else
			{
				$table_data[] = [
					['type' => 'text', 'text' => $pos],
					['type' => 'text', 'text' => $row->name],
					['type' => 'text', 'text' => $row->email],
					['type' => 'text', 'text' => $row->str_created_at]
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
					'action' => url('user'),
					'role' => 'form',
					'data-type' => 'table list',
					'data-title' => 'User',
					'data-endpoint' => url('user'),
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
								'label' => 'Nama Lengkap / Email', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'name_or_email',
								]
							],
						]
					],
					[
						'css_class' => 'col-lg-6',
						'fields' => [
							[
								'label' => 'Tanggal Daftar', 
								'element' => 'datepicker_range',
								'attr' => [
									'name' => 'created_at',
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
				'name_or_email' => $name_or_email,
				'created_at' => [$created_at[0], $created_at[1]]
			],
			
			'total_records' => $total_records,
			'total_page' => $total_pages,
			
			'table_header' => $table_header,
			
			'table_data' => $table_data
		];
		
		//punya akses post, kasih button input user
		//~ if(Auth::user()->hasMenu($this->menu_id, 'post'))
		if(User::find($user_id)->hasMenu($this->menu_id, 'post'))
		{
			$return_result['form']['elements_blok'][3]['fields'][] = [
				'label' => '<span class="glyphicon glyphicon-plus"></span> Tambah User', 
				'element' => 'button',
				'attr' => [
					'type' => 'button',
					'class' => 'btn btn-success',
					'data-title' => 'Input User',
					'data-endpoint' => url('user/create'),
					'data-method' => 'get'
				],
				'redirect' => [
					'url' => url('user'),
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
		//tidak punya akses post forbidden
		$user_id = $request->user()->id;
		$redirect_url = $request->input('redirect');
		
		if(!User::find($user_id)->hasMenu($this->menu_id, 'post'))
		{
			return response('forbidden', 403);
		}
		
		$return_result = [
			'type' => 'input form',
			'form' => [
				'attr' => [
					'method' => 'post',
					'action' => url('user'),
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
								'label' => 'Alamat Email', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'email',
								]
							],
							[
								'label' => 'Nama Lengkap', 
								'element' => 'input',
								'attr' => [
									'type' => 'text', 
									'name' => 'name',
								]
							],
							[
								'label' => 'Password', 
								'element' => 'input',
								'attr' => [
									'type' => 'password', 
									'name' => 'password',
								]
							],
							[
								'label' => 'Konfirmasi Password', 
								'element' => 'input',
								'attr' => [
									'type' => 'password', 
									'name' => 'cpassword',
								]
							],
						]
					],
					
					[
						'css_class' => 'clearfix visible-lg-block',
						'fields' => []
					],
					
					[
						'css_class' => 'col-lg-12',
						'fields' => [
							[
								'label' => '<i class="glyphicon glyphicon-ok-circle"></i> Centang Semua Menu', 
								'element' => 'button',
								'attr' => [
									'type' => 'button', 
									'class' => 'btn btn-primary',
									'style' => 'margin:0px 5px 5px 0px',
									'onclick' => 'checkAll(1)'
								]
							],
							[
								'label' => '<i class="glyphicon glyphicon-ok-circle"></i> Centang Semua View', 
								'element' => 'button',
								'attr' => [
									'type' => 'button', 
									'class' => 'btn btn-success',
									'style' => 'margin:0px 5px 5px 0px',
									'onclick' => 'checkAllDefault()'
								]
							],
							[
								'label' => '<i class="glyphicon glyphicon-ban-circle"></i> Hapus Semua Centang', 
								'element' => 'button',
								'attr' => [
									'type' => 'button', 
									'class' => 'btn btn-danger',
									'style' => 'margin:0px 5px 5px 0px',
									'onclick' => 'checkAll(0)'
								]
							],
						]
					],
					
					
					[
						'css_class' => 'col-lg-4',
						'fields' => []
					],
					
					[
						'css_class' => 'col-lg-4',
						'fields' => []
					],
					
					[
						'css_class' => 'col-lg-4',
						'fields' => []
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
									'data-title' => 'User',
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
		
		$menus = Menu::orderBy('parent_id')
			->orderBy('rank')
			->get();
		
		$pos = 2;
		foreach($menus as $row)
		{
			if(!is_null($row->api_endpoint))
			{
				$pos ++;
				if($pos > 5) {$pos = 3;}
				
				$return_result['form']['elements_blok'][$pos]['fields'][] = [
					'label' => $row->title, 
					'element' => 'checkbox',
					'options' => [
						[
							'name' => 'menu['.$row->id.'][]',
							'value' => 'get',
							'label' => 'View',
							'default' => 1 //default == true, jika field yang lain di check, field ini otomatis check
						],
						[
							'name' => 'menu['.$row->id.'][]',
							'value' => 'post',
							'label' => 'Input',
							'default' => 0 //default == true, jika field yang lain di check, field ini otomatis check
						],
						[
							'name' => 'menu['.$row->id.'][]',
							'value' => 'put',
							'label' => 'Edit',
							'default' => 0 //default == true, jika field yang lain di check, field ini otomatis check
						],
						[
							'name' => 'menu['.$row->id.'][]',
							'value' => 'delete',
							'label' => 'Delete',
							'default' => 0 //default == true, jika field yang lain di check, field ini otomatis check
						],
					]
				];
			}
		}
		
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
		if(!User::find($request->user()->id)->hasMenu($this->menu_id, 'post'))
		{
			return response('Forbidden', 403);
		}

		//validate
		$this->validate(
			$request, 
			[
				'email' => 'required|max:255|unique:users,email',
				'name' => 'required|max:255',
				'password' => 'required|same:cpassword|min:8',
				'cpassword' => 'required_with:password',
				'menu' => 'required'
			],
			[
				'email.required' => '<span style="font-weight:bold;font-style:italic">Alamat Email</span> harap di isi',
				'email.unique' => '<span style="font-weight:bold;font-style:italic">'.$request->input('email').'</span> sudah terdaftar. Harap pilih alamat email yang lain!',
				'name.required' => '<span style="font-weight:bold;font-style:italic">Nama Lengkap</span> harap di isi',
				'password.required' => '<span style="font-weight:bold;font-style:italic">Password</span> harap di isi',
				'password.same' => '<span style="font-weight:bold;font-style:italic">Password</span> dan <span style="font-weight:bold;font-style:italic">Konfirmasi Password</span> tidak sama',
				'password.min' => '<span style="font-weight:bold;font-style:italic">Password</span> minimal 8 karakter',
				'cpassword.required_with' => '<span style="font-weight:bold;font-style:italic">Konfirmasi Password</span> harap di isi',
				'menu.required' => 'Harap centang salah satu menu'
			]
		);
		
		$user = new User;
		$user->name = $request->input('name');
		$user->email = $request->input('email');
		$user->password = bcrypt($request->input('password'));
		if($user->save())
		{
			//save menu
			$menus = $request->input('menu');
			foreach($menus as $menu_id=>$data)
			{
				$menu = Menu::find($menu_id);
				
				if($menu)
				{
					$method_post = false;
					$method_put = false;
					$method_delete = false;
					
					foreach($data as $row)
					{
						switch($row)
						{
							case 'post':
								$method_post = true;
								break;
							case 'put':
								$method_put = true;
								break;
							case 'delete':
								$method_delete = true;
								break;
						}
					}
					
					$user->assignMenu($menu_id, $method_post, $method_put, $method_delete);
					if($menu->parent_id != 0)
					{
						$this->asign_parent_menu($menu->parent_id, $user->id);
					}
				}
			}
			
			return response([
				'url' => '',
				'api_endpoint' => url('user'),
				'api_method' => 'GET',
				'title' => 'User'
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
		if($id == 'profile')
		{
			$user_id = $request->user()->id;
			$user = User::find($user_id);
			
			$result = [
				'type' => 'input form',
				'form' => [
					'attr' => [
						'method' => 'put',
						'action' => url('user/profile'),
						'role' => 'form'
					],
				
					'hidden' => [
						['name' => '_token', 'value' => csrf_token()],
					],
					
					'elements_blok' => [
						[
							'css_class' => 'col-lg-6',
							'fields' => [
								[
									'label' => 'Alamat Email', 
									'element' => 'input',
									'attr' => [
										'type' => 'text', 
										'name' => 'email',
										'value' => $user->email,
										'readonly' => 'readonly'
									]
								],
								[
									'label' => 'Nama Lengkap', 
									'element' => 'input',
									'attr' => [
										'type' => 'text', 
										'name' => 'name',
										'value' => $user->name
									]
								],
								[
									'label' => 'Password', 
									'element' => 'input',
									'attr' => [
										'type' => 'password', 
										'name' => 'password',
										'placeholder' => 'Isi untuk mengganti password'
									]
								],
								[
									'label' => 'Konfirmasi Password', 
									'element' => 'input',
									'attr' => [
										'type' => 'password', 
										'name' => 'cpassword',
										'placeholder' => 'Isi untuk mengganti password'
									]
								],
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
										'class' => 'btn btn-warning'
									]
								]
							]
						]
					]
				],
				'form_data' => [
					'name' => $user->name,
					'email' => $user->email
				]
			];
			
			return response($result, 200);
		}
		
		return response('', 404);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit(Request $request, $id)
	{
		if(!User::find($request->user()->id)->hasMenu($this->menu_id, 'put'))
		{
			return response('Forbidden', 403);
		}
		
		$user = User::findOrFail($id);
		
		$return_result = $this->create($request)->original;
		
		//set action
		$return_result['form']['attr']['action'] = url('user/'.$id);
		
		//set method
		$return_result['form']['attr']['method'] = 'put';
		
		//buang password
		$tampung = array_pop($return_result['form']['elements_blok'][0]['fields']);
		$tampung = array_pop($return_result['form']['elements_blok'][0]['fields']);
		
		$return_result['form_data'] = [
			'name' => $user->name,
			'email' => $user->email
		];
		
		foreach($user->menus as $row)
		{
			$value = array('get');
			if($row->pivot->method_post){array_push($value, 'post');}
			if($row->pivot->method_put){array_push($value, 'put');}
			if($row->pivot->method_delete){array_push($value, 'delete');}
			
			$return_result['form_data']['menu['.$row->id.'][]'] = $value;
		}
		
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
		if($id == 'profile')
		{
			//validate
			$this->validate(
				$request, 
				[
					'name' => 'required|max:255',
					'password' => 'same:cpassword|min:8',
					'cpassword' => 'required_with:password'
				],
				[
					'name.required' => '<span style="font-weight:bold;font-style:italic">Nama Lengkap</span> harap di isi',
					'password.same' => '<span style="font-weight:bold;font-style:italic">Password</span> dan <span style="font-weight:bold;font-style:italic">Konfirmasi Password</span> tidak sama',
					'password.min' => '<span style="font-weight:bold;font-style:italic">Password</span> minimal 8 karakter',
					'cpassword.required_with' => '<span style="font-weight:bold;font-style:italic">Konfirmasi Password</span> harap di isi'
				]
			);
			
			//get user
			$user_id = $request->user()->id;
			$user = User::find($user_id);
			
			//redirect to logout if change password
			$redirect_url = '';
			
			//set user
			$user->name = $request->name;
			if($request->password){
				$user->password = bcrypt($request->password);
				$redirect_url = url('logout');
			}
			
			//save user
			if($user->save())
			{
				return response([
					'url' => $redirect_url,
					'api_endpoint' => url('user/profile'),
					'api_method' => 'GET',
					'title' => 'User Profile'
				], 200);
			}
			
			return response('', 500);
		}
		elseif(is_numeric($id))
		{
			if(!User::find($request->user()->id)->hasMenu($this->menu_id, 'put'))
			{
				return response('Forbidden', 403);
			}
			
			//validate
			$this->validate(
				$request, 
				[
					'email' => 'required|max:255|unique:users,email,'.$id,
					'name' => 'required|max:255',
					'menu' => 'required'
				],
				[
					'email.required' => '<span style="font-weight:bold;font-style:italic">Alamat Email</span> harap di isi',
					'name.required' => '<span style="font-weight:bold;font-style:italic">Nama Lengkap</span> harap di isi',
					'menu.required' => 'Harap centang salah satu menu'
				]
			);
			
			$redirect_endpoint = $request->input('_redirect', url('user'));
			
			//get user
			$user = User::findOrFail($id);
			
			//save user
			$user->name = $request->input('name');
			$user->email = $request->input('email');
			
			if($user->save())
			{
				//detach all menu
				$user->revokeMenu();
				
				//save menu
				$menus = $request->input('menu');
				foreach($menus as $menu_id=>$data)
				{
					$menu = Menu::find($menu_id);
					
					if($menu)
					{
						$method_post = false;
						$method_put = false;
						$method_delete = false;
						
						foreach($data as $row)
						{
							switch($row)
							{
								case 'post':
									$method_post = true;
									break;
								case 'put':
									$method_put = true;
									break;
								case 'delete':
									$method_delete = true;
									break;
							}
						}
						
						$user->assignMenu($menu_id, $method_post, $method_put, $method_delete);
						if($menu->parent_id != 0)
						{
							$this->asign_parent_menu($menu->parent_id, $user->id);
						}
					}
				}
				
				return response([
					'url' => '',
					'api_endpoint' => $redirect_endpoint,
					'api_method' => 'GET',
					'title' => 'User'
				], 200);
			}
			
			return response('', 500);
		}
		
		return response('', 404);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy(Request $request, $id)
	{
		if(!User::find($request->user()->id)->hasMenu($this->menu_id, 'delete'))
		{
			return response('Forbidden', 403);
		}

		if(is_numeric($id) && $id != $request->user()->id)
		{
			$user = User::find($id);
			if(empty($user)){return response('not found', 404);}
			
			//detach all menu
			$user->revokeMenu();
			
			//delete menu
			if($user->delete())
			{
				return response(['result' => true], 200);
			}
			return response('server error', 500);
		}
		return response('bad request', 400);
	}
	
	private function asign_parent_menu($menu_id, $user_id)
	{
		$menu = Menu::find($menu_id);
		$user = User::find($user_id);
		
		if(!$user->hasMenu($menu_id))
		{
			$user->assignMenu($menu_id);
			if($menu->parent_id != 0)
			{
				$this->asign_parent_menu($menu->parent_id, $user_id);
			}
		}
	}
}
