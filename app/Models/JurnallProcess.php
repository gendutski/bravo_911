<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class JurnallProcess extends Model
{
	protected $table = 'jurnall_processes';
	protected $primaryKey = 'id';
	
	public function account_code()
	{
		return $this->belongsTo('App\Models\AccountCode', 'account_code_id');
	}
	
	public function scopeJoinAccountCode($query, $select = 0)
	{
		$query->join('account_codes', 'account_codes.id', '=', $this->table.'.account_code_id');
		switch($select)
		{
			case 1:
				//
				break;
			default:
				$query
					->addSelect('account_codes.kode')
					->addSelect($this->table.'.*')
					->addSelect(DB::raw("to_char({$this->table}.tanggal, 'DD-Mon-YYYY HH24:MI:SS') as str_tanggal"));
				break;
		}
		
		return $query;
	}
	
	public function scopeSearch($query, $search = array())
	{
		if(!empty($search['tanggal']) && is_array($search['tanggal']) && count($search['tanggal']) == 2)
		{
			if(!empty($search['tanggal'][0]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['tanggal'][0]))
			{
				$query->where('tanggal', '>=', DB::raw("'{$search['tanggal'][0]}'::date"));
			}
			if(!empty($search['tanggal'][1]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['tanggal'][1]))
			{
				$query->where('tanggal', '<=', DB::raw("'{$search['tanggal'][1]}'::date"));
			}
		}
		
		return $query;
	}
}
