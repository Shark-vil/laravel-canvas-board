<template>
	<div>
		<board-paint v-for="entry in dataList" :key="entry.id"
			:id="entry.id"
			:name="entry.uuid"
			:typeId="entry.type"
			:x="entry.x"
			:y="entry.y"
			:width="entry.width"
			:height="entry.height"
			:scaleX="entry.scaleX"
			:scaleY="entry.scaleY"
			:rotation="entry.rotation"
			:points="entry.property.points"
			ref="vueNodes" />

			<board-paint ref="newNode"
				:id="newNodeData.id"
				:name="newNodeData.uuid"
				:typeId="newNodeData.type"
				:x="newNodeData.x"
				:y="newNodeData.y"
				:width="newNodeData.width"
				:height="newNodeData.height"
				:scaleX="newNodeData.scaleX"
				:scaleY="newNodeData.scaleY"
				:rotation="newNodeData.rotation"
				:points="newNodeData.points" />
	</div>
</template>

<script>
import { NodeBootstrap } from './base/NodeBootstrap';

export default {
	data() {
		return  {
			dataList: [],
			hasPaint: false,
			newNodeData: {
				id: -1,
				uuid: '',
				type: 'paint',
				x: undefined,
				y: undefined,
				width: undefined,
				height: undefined,
				scaleX: undefined,
				scaleY: undefined,
				rotation: undefined,
				points: []
			}
		}
	},
	computed: {
		newNode() {
			return this.$refs.newNode;
		}
	},
	mounted() {
		const bootstrap = new NodeBootstrap(this, 'paint');
	},
	methods: {
		StartPaint: function() {
			this.hasPaint = true;
		},
		StopPaint: function() {
			this.hasPaint = false;

			const formData = new FormData();
			formData.append('points', JSON.stringify(this.newNodeData.points));

			axios.post('/api/board/paint/add', formData,
			{
				headers: { 'Content-Type': 'multipart/form-data' }
			}).catch((error) => {
				this.$notify({
					title: `Не удалось добавить рисунок`,
					text: error,
					type: 'error'
				});
			});

			this.newNode.ResetPoints();
		},
		MouseUpHandler: function(e) {
			if (!this.hasPaint) return;
			this.newNode.MouseUpHandler(e);
		},
		MouseMoveHandler: function(e) {
			if (!this.hasPaint) return;
			this.newNode.MouseMoveHandler(e);
		},
		MouseDownHandler: function(e) {
			if (!this.hasPaint) return;
			this.newNode.MouseDownHandler(e);
		},
	}
}
</script>