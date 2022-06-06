<?php

namespace App\Http\Controllers\Api\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoardNode;
use Illuminate\Support\Str;

class TextController extends Controller
{
	public function add(Request $request)
	{
		$createData = [];
		$property = [];

		if ($request->has('x'))
			$createData['x'] = $request->input('x');

		if ($request->has('y'))
			$createData['y'] = $request->input('y');

		if ($request->has('width'))
			$createData['width'] = $request->input('width');

		if ($request->has('height'))
			$createData['height'] = $request->input('height');

		if ($request->has('scaleX'))
			$createData['scaleX'] = $request->input('scaleX');

		if ($request->has('scaleY'))
			$createData['scaleY'] = $request->input('scaleY');

		if ($request->has('rotation'))
			$createData['rotation'] = $request->input('rotation');

		if ($request->has('fontSize'))
			$property['fontSize'] = $request->input('fontSize');
		
		$property['text'] = $request->input('text');

		$createData['uuid'] = Str::uuid();
		$createData['type'] = 'text';
		$createData['property'] = json_encode($property);

		$entry = BoardNode::create($createData);

		return response()->json($entry);
	}

	public function delete(int $id)
	{
		$entry = BoardNode::where('id', $id)->where('type', 'text')->first();
		$entry->delete();
		return response()->json($entry);
	}

	public function update(Request $request, int $id)
	{
		$x = $request->input('x');
		$y = $request->input('y');
		$width = $request->input('width');
		$height = $request->input('height');
		$scaleX = $request->input('scaleX');
		$scaleY = $request->input('scaleY');
		$rotation =  $request->input('rotation');
		$text =  $request->input('text');

		$entry = BoardNode::where('id', $id)->where('type', 'text')->first();

		if ($request->has('x') && is_numeric($x))
			$entry->x = floatval($x);

		if ($request->has('y') && is_numeric($y))
			$entry->y = floatval($y);

		if ($request->has('scaleX') && is_numeric($scaleX))
			$entry->scaleX = floatval($scaleX);

		if ($request->has('scaleY') && is_numeric($scaleY))
			$entry->scaleY = floatval($scaleY);

		if ($request->has('width') && is_numeric($width))
			$entry->width = floatval($width);

		if ($request->has('height') && is_numeric($height))
			$entry->height = floatval($height);

		if ($request->has('rotation') && is_numeric($rotation))
			$entry->rotation = floatval($rotation);

		if ($request->has('text'))
			$entry->setProperty([
				'text' => $text,
			]);

		$entry->save();

		return response()->json($entry);
	}

	public function get(int $id = null)
	{
		if ($id != null)
		{
			return response()->json(BoardNode::where('id', $id)->where('type', 'text')->first());
		}
		else
		{
			return response()->json(BoardNode::where('type', 'text')->get());
		}
	}
}
