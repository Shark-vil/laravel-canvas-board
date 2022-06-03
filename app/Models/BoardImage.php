<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardImage extends Model
{
    use HasFactory;

		protected $fillable = [
			'path',
			'x',
			'y',
			'scaleX',
			'scaleY',
			'width',
			'height',
			'rotation'
	];
}
