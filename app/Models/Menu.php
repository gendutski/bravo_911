<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
	protected $table = 'menus';
	protected $primaryKey = 'id';
	public $timestamps = false;
	
	public function users()
	{
		return $this->belongsToMany('App\Models\User', 'user_menu', 'menu_id', 'user_id');
	}
}
