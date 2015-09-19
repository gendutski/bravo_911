<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCode extends Model
{
	protected $table = 'account_codes';
	protected $primaryKey = 'id';
	
	public function code_type()
	{
		return $this->belongsTo('App\Models\AccountCodeType', 'id', 'type_id');
	}
	
	public function jurnall_process()
	{
		return $this->hasMany('App\Models\JurnallProcess', 'account_code_id');
	}
	
	public function scopeSearch($query, $search = array())
	{
		if(!empty($search['kode']))
		{
			$query->where('kode', '=', $search['kode']);
		}
		
		if(!empty($search['uraian_rekening']))
		{
			$query->where('uraian_rekening', '=', $search['uraian_rekening']);
		}
		
		return $query;
	}
}
