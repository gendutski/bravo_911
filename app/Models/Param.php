<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Param extends Model
{
	protected $table = 'params';
	protected $primaryKey = 'id';
	
	public function scopeSearch($query, $search = array())
	{
		if(!empty($search['type']))
		{
			$query->where('type', '=', $search['type']);
		}
		
		return $query;
	}
}
