<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountCodeType extends Model
{
	protected $table = 'account_code_types';
	protected $primaryKey = 'id';
	public $timestamps = false;
	
	public function account_code()
	{
		return $this->hasMany('App\Models\AccountCode', 'type_id');
	}
	
	public function scopeDropDown($query)
	{
		$dbresult = $query->get();
		
		$result = array();
		foreach($dbresult as $row)
		{
			$options = array();
			foreach($row->account_code as $row2)
			{
				$options[] = [
					'html' => $row2->kode.' '.$row2->uraian_rekening,
					'attr' => [
						'value' => $row2->id
					]
				];
			}
			
			$result[] = [
				'label' => $row->name,
				'options' => $options
			];
		}
		return $result;
	}
}
