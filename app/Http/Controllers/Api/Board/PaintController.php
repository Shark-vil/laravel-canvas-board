<?php

namespace App\Http\Controllers\Api\Board;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File; 
use App\Models\BoardNode;
use Illuminate\Support\Str;
use App\Events\NodeTransform;
use App\Events\NodeCreated;
use App\Events\NodeRemoved;

class PaintController extends Controller
{
	public function add(Request $request)
	{
		$createData = [];

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
	
		$createData['uuid'] = Str::uuid();
		$createData['type'] = 'paint';
		$createData['property'] = json_encode([
			'points' => json_decode($request->input('points')),
		]);

		$entry = BoardNode::create($createData);

		broadcast(new NodeCreated($entry));

		return response()->json($entry);
	}

	public function delete(int $id)
	{
		$entry = BoardNode::where('id', $id)->where('type', 'paint')->first();
		$entry->delete();

		broadcast(new NodeRemoved($entry));
		
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

		$entry = BoardNode::where('id', $id)->where('type', 'paint')->first();

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

		$entry->save();

		broadcast(new NodeTransform($entry));

		return response()->json($entry);
	}

	public function get(int $id = null)
	{
		if ($id != null)
		{
			return response()->json(BoardNode::where('id', $id)->where('type', 'paint')->first());
		}
		else
		{
			return response()->json(BoardNode::where('type', 'paint')->get());
		}
	}
}
