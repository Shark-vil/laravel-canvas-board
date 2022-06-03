@section('styles')
	<style>
		.canvas-container {
			width: 100%;
			height: 100%;
			background-color: rgb(228, 221, 212);
		}
	</style>
@endsection

<x-app-layout>
	<div class="canvas-container">
		<canvas-board></canvas-board>
	</div>
</x-app-layout>
