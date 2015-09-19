<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\User;

use App\Http\Controllers\UserController;

class HomeController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		$user_id = $request->user()->id;
		$menu = User::find($user_id)
			->menus()
			->orderBy('parent_id')
			->orderBy('rank')
			->get();
		
		$data['menu'] = $this->generate_menu($menu, 0);
		$user_controller = new UserController;
		$data['user_profile'] = $user_controller->show($request, 'profile')->original['form_data'];
		
		return view('home', $data);
	}
	
	private function generate_menu($data, $parent_id)
	{
		$result = array();
		
		foreach($data as $row)
		{
			if($row->parent_id == $parent_id)
			{
				$result[] = [
					'name' => $row->name,
					'title' => $row->title,
					'api_endpoint' => (!empty($row->api_endpoint)? url($row->api_endpoint):''),
					'api_method' => 'get',
					'allow_method' => [
						'post' => $row->pivot->method_post,
						'put' => $row->pivot->method_put,
						'delete' => $row->pivot->method_delete
					],
					'children' => $this->generate_menu($data, $row->id)
				];
			}
		}
		
		return $result;
	}
}
