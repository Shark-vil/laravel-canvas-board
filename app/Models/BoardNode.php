<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOption\None;

class BoardNode extends Model
{
	use HasFactory;

	protected $fillable = [
		'uuid',
		'type',
		'x',
		'y',
		'scaleX',
		'scaleY',
		'width',
		'height',
		'rotation',
		'property'
	];

	public function getPropertyAttribute($value) {
		return json_decode($value);
	}

	public function getProperty() : string
	{
		return json_decode($this->property);
	}

 	public function setProperty($data)
	{
		$this->property = json_encode($data);
	}
}
