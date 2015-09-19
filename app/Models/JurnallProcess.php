<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JurnallProcess extends Model
{
	protected $table = 'jurnall_processes';
	protected $primaryKey = 'id';
	
	public function account_code()
	{
		return $this->belongsTo('App\Models\AccountCode', 'account_code_id');
	}
	
	public function scopeSearch($query, $search = array())
	{
		if(!empty($search['tanggal']) && is_array($search['tanggal']) && count($search['tanggal']) == 2)
		{
			if(!empty($search['tanggal'][0]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['tanggal'][0]))
			{
				$query->where('tanggal', '>=', DB::raw("'{$search['tanggal'][0]}'::timestamp without time zone"));
			}
			if(!empty($search['tanggal'][1]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['tanggal'][1]))
			{
				$query->where('tanggal', '<=', DB::raw("'{$search['tanggal'][1]}'::timestamp without time zone"));
			}
		}
		
		return $query;
	}
}
