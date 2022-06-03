@extends('layouts.app')

@section('styles')
	<style>
		.canvas-container {
			width: 100%;
			height: 100%;
			background-color: rgb(228, 221, 212);
		}
	</style>
@endsection

@section('content')
	<div class="canvas-container">
		<canvas-board></canvas-board>
	</div>
@endsection