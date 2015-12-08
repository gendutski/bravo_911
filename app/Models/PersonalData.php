<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class PersonalData extends Model
{
    protected $table = 'personal_datas';
	protected $primaryKey = 'id';
	
	
	public function position()
	{
		return $this->belongsTo('App\Models\Param', 'posisi');
	}
		
	public function scopeJoinAgama($query)
	{
		$query->join('params as tbl_agama', 'tbl_agama.id', '=', $this->table.'.agama');
		$query->addSelect('tbl_agama.name as nama_agama');
		
		return $query;
	}
		
	public function scopeJoinPosition($query)
	{
		$query->join('params as tbl_posisi', 'tbl_posisi.id', '=', $this->table.'.posisi');
		$query->addSelect('tbl_posisi.name as nama_posisi');
		
		return $query;
	}
	
	public function scopeSearch($query, $search = array())
	{
		$query->select($this->table.'.*')
			  ->addSelect(DB::raw("to_char({$this->table}.tgl_lahir, 'DD-Mon-YYYY') as str_tgl_lahir"));
		
		if(!empty($search['nama_lengkap']))
		{
			$query->where($this->table.'.nama_lengkap', 'ilike', '%'.$search['nama_lengkap'].'%');
		}
		
		if(!empty($search['agama']))
		{
			$query->where($this->table.'.agama', '=', $search['agama']);
		}
		
		if(!empty($search['posisi']))
		{
			$query->where($this->table.'.posisi', '=', $search['posisi']);
		}
		
		if(!empty($search['tgl_lahir']))
		{
			if(!empty($search['tgl_lahir'][0]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['tgl_lahir'][0]))
			{
				$query->where('tgl_lahir', '>=', DB::raw("'{$search['tgl_lahir'][0]}'::date"));
			}
			if(!empty($search['tgl_lahir'][1]) && preg_match("#^[0-9]{4}-[0-9]{2}-[0-9]{2}$#", $search['tgl_lahir'][1]))
			{
				$query->where('tgl_lahir', '<=', DB::raw("'{$search['tgl_lahir'][1]}'::date"));
			}
		}
		
		if(!empty($search['jenis_kelamin']))
		{
			if($search['jenis_kelamin'] == 'pria' || $search['jenis_kelamin'] == 'wanita')
			{
				$query->where($this->table.'.jenis_kelamin', '=', $search['jenis_kelamin']);
			}
		}
		
		if(!empty($search['type']))
		{
			if($search['type'] == 'hr-project')
			{
				$query->where('tbl_posisi.type', '=', 'outsource-position');
			}
			elseif($search['type'] == 'hr-staf')
			{
				$query->where('tbl_posisi.type', '=', 'staff-position');
			}
		}
		
		return $query;
	}
	
}
