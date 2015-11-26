<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;

class User extends Model implements AuthenticatableContract,
									AuthorizableContract,
									CanResetPasswordContract
{
	use Authenticatable, Authorizable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
	
	public function assignMenu($menu, $method_post = false, $method_put = false, $method_delete = false)
	{
		$method = [
			'method_post' => $method_post,
			'method_put' => $method_put,
			'method_delete' => $method_delete
		];
		
		return $this->menus()->attach($menu, $method);
	}
	
	public function hasMenu($menu_id, $method = '')
	{
		foreach($this->menus as $menu)
		{
			if($menu->id == $menu_id)
			{
				switch($method)
				{
					case 'post':
						return $menu->pivot->method_post;
						break;
					case 'put':
						return $menu->pivot->method_put;
						break;
					case 'delete':
						return $menu->pivot->method_delete;
						break;
					default:
						return true;
						break;
				}
			}
		}
		
		return false;
	}
	
	public function menus()
	{
		return $this->belongsToMany('App\Models\Menu', 'user_menu', 'user_id', 'menu_id')->withPivot('method_post', 'method_put', 'method_delete');
	}
	
	public function revokeMenu($menu = null)
	{
		if(!empty($menu))
		{
			return $this->menus()->detach($menu);
		}
		else
		{
			return $this->menus()->detach();
		}
	}
	
	public function scopeSearch($query, $search = array())
	{
		if(!empty($search['name_or_email']))
		{
			$query->orWhere(function($qry) use($search){
				$qry
					->where('name', 'ilike', '%'.$search['name_or_email'].'%')
					->where('email', 'ilike', '%'.$search['name_or_email'].'%');
			});
		}
		
		if(!empty($search['created_at']) && is_array($search['created_at']) && count($search['created_at']) == 2)
		{
			if(!empty($search['created_at'][0]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['created_at'][0]))
			{
				$query->where('created_at', '>=', DB::raw("'{$search['created_at'][0]}'::timestamp without time zone"));
			}
			if(!empty($search['created_at'][1]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['created_at'][1]))
			{
				$query->where('created_at', '<=', DB::raw("'{$search['created_at'][1]}'::timestamp without time zone"));
			}
		}
		
		return $query;
	}
	
}
