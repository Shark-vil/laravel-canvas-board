<template></template>

<script>
export default {
	mounted() {
		const stage = this.$boardInstance.konvaStage;
		const scaleBy = 1.05;
		const GetMouseTransform = this.GetMouseTransform;

		window.addEventListener('keydown', function (e) {
			if (!e.shiftKey) return;
			const mouseTransform = GetMouseTransform();
			const pointer = mouseTransform.pointer;
			const mousePointTo = mouseTransform.mouse;

			stage.scale({ x: 1, y: 1 });

			const newPos = {
				x: pointer.x - mousePointTo.x,
				y: pointer.y - mousePointTo.y,
			};

			stage.position(newPos);
		});

		stage.on('wheel', (e) => {
			e.evt.preventDefault();

			const oldScale = stage.scaleX();
			const mouseTransform = GetMouseTransform();
			const pointer = mouseTransform.pointer;
			const mousePointTo = mouseTransform.mouse;

			let direction = -(e.evt.deltaY > 0 ? 1 : -1);
			if (e.evt.ctrlKey) direction = -direction;

			const newScale = direction > 0 ? oldScale * scaleBy : oldScale / scaleBy;

			stage.scale({ x: newScale, y: newScale });

			const newPos = {
				x: pointer.x - mousePointTo.x * newScale,
				y: pointer.y - mousePointTo.y * newScale,
			};

			stage.position(newPos);
		});
	},
	methods: {
		GetMouseTransform: function() {
			const stage = this.$boardInstance.konvaStage;
			const scale = stage.scaleX();
			const pointer = stage.getPointerPosition();
			const mousePointTo = {
				x: (pointer.x - stage.x()) / scale,
				y: (pointer.y - stage.y()) / scale,
			};
			return {
				mouse: mousePointTo,
				pointer: pointer,
			};
		}
	}
}
</script>