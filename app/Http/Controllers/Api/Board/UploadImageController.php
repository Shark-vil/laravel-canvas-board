<?php

namespace App\Http\Controllers\Api\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use App\Models\BoardImage;

class UploadImageController extends Controller
{
	public function upload(Request $request)
	{
		$xPosition = $request->input('x');
		$yPosition = $request->input('y');
		$width = $request->input('width');
		$height = $request->input('height');
		$scaleX = $request->input('scaleX');
		$scaleY = $request->input('scaleY');
		$rotation =  $request->input('rotation');
		$path = $request->file('image')->store('public/board/images');

		$entry = BoardImage::create([
			'path' => substr($path, 6),
			'x' => $xPosition,
			'y' => $yPosition,
			'width' => $width,
			'height' => $height,
			'scaleX' => $scaleX,
			'scaleY' => $scaleY,
			'rotation' => $rotation
		]);

		return response()->json($entry);
	}

	public function delete(int $id)
	{
		$entry = BoardImage::where('id', $id)->first();
		Storage::delete('public' . $entry->path);
		$entry->delete();
		return response()->json($entry);
	}

	public function update(Request $request, int $id)
	{
		$xPosition = $request->input('x');
		$yPosition = $request->input('y');
		$width = $request->input('width');
		$height = $request->input('height');
		$scaleX = $request->input('scaleX');
		$scaleY = $request->input('scaleY');
		$rotation =  $request->input('rotation');

		$entry = BoardImage::where('id', $id)->first();

		if (isset($xPosition) && !empty($xPosition) && is_numeric($xPosition))
			$entry->x = floatval($xPosition);

		if (isset($yPosition) && !empty($yPosition) && is_numeric($yPosition))
			$entry->y = floatval($yPosition);

		if (isset($scaleX) && !empty($scaleX) && is_numeric($scaleX))
			$entry->scaleX = floatval($scaleX);

		if (isset($scaleY) && !empty($scaleY) && is_numeric($scaleY))
			$entry->scaleY = floatval($scaleY);

		if (isset($width) && !empty($width) && is_numeric($width))
			$entry->width = floatval($width);

		if (isset($height) && !empty($height) && is_numeric($height))
			$entry->height = floatval($height);

		if (isset($rotation) && !empty($rotation) && is_numeric($rotation))
			$entry->rotation = floatval($rotation);

		$entry->save();

		return response()->json($entry);
	}

	public function get(int $id = null)
	{
		if ($id != null)
		{
			return response()->json(BoardImage::where('id', $id)->first());
		}
		else
		{
			return response()->json(BoardImage::all());
		}
	}
}
