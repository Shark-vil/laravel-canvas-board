<template>
	<v-line ref="root" :config="nodeConfig" />
</template>

<script>
import { Task } from '../task';

export default {
	props: [
		'id',
		'typeId',
		'name',
		'width',
		'height',
		'x',
		'y',
		'rotation',
		'scaleX',
		'scaleY',
		'points',
	],
	computed: {
		konvaNode() {
			return this.$refs.root.getNode();
		}
	},
	data() {
		return {
			isDrawing: false,
			nodeConfig: {
				uniqueId: -1,
				typeId: '',
				name: '',
				scaleX: 1,
				scaleY: 1,
				x: 0,
				y: 0,
				rotation: 0,
				draggable: true,
				stroke: 'black',
				points: [],
			}
		}
	},
	created() {
		const nodeConfig = this.nodeConfig;
		nodeConfig.name = this.name;
		nodeConfig.typeId = this.typeId;
		nodeConfig.uniqueId = parseInt(this.id);

		if (this.points != undefined) nodeConfig.points = this.points;
		if (this.scaleX != undefined) nodeConfig.scaleX = parseFloat(this.scaleX);
		if (this.scaleY != undefined) nodeConfig.scaleY = parseFloat(this.scaleY);
		if (this.x != undefined) nodeConfig.x = parseInt(this.x);
		if (this.y != undefined) nodeConfig.y = parseInt(this.y);
		if (this.rotation != undefined) nodeConfig.rotation = parseFloat(this.rotation);
	},
	methods: {
		Modify: function(entry) {
			const konvaNode = this.konvaNode;
			konvaNode.x(entry.x);
			konvaNode.y(entry.y);
			konvaNode.scaleX(entry.scaleX);
			konvaNode.scaleY(entry.scaleY);
			konvaNode.rotation(entry.rotation);
		},
		Delete: async function(qsilentErrors = false) {
			await axios.post('/api/board/paint/delete/' + this.id).then(response => {
				this.$notify({
					title: `Удаление рисунка: ${this.id}`,
					text: 'Рисунок удалён',
					type: 'success'
				});
			}).catch((error) => {
				if (!qsilentErrors)
					this.$notify({
						title: `Удаление рисунка: ${this.id}`,
						text: 'Не удалось удалить рисунок',
						type: 'error'
					});

					console.error(error);
			});
		},
		ResetPoints: function() {
			this.nodeConfig.points.splice(0, this.nodeConfig.points.length);
		},
		MouseDownHandler(e) {
      this.isDrawing = true;
			const point =  e.target.getStage().getRelativePointerPosition();
			this.nodeConfig.points.push(point.x, point.y);
    },
    MouseMoveHandler(e) {
      if (!this.isDrawing) {
        return;
      }

      const stage = e.target.getStage();
      const point = stage.getRelativePointerPosition();

			this.nodeConfig.points.push(point.x, point.y)
    },
    MouseUpHandler() {
      this.isDrawing = false;
    },
	}
}
</script>