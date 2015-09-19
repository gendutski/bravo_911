<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionType extends Model
{
	protected $table = 'position_types';
	protected $primaryKey = 'id';
	public $timestamps = false;
	
	public function position()
	{
		$this->hasMany('App\Models\Position', 'position_types_id');
	}
}
