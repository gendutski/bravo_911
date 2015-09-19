<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
	protected $table = 'religions';
	protected $primaryKey = 'id';
	public $timestamps = false;
	
	public function personal_data()
	{
		return $this->hasMany('App\Models\PersonalData', 'religion_id');
	}
}
