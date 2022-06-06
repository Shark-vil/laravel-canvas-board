<template>
	<v-transformer :config="nodeConfig" ref="root" />
</template>

<script>
export default {
	computed: {
		konvaNode() {
			return this.$refs.root.getNode();
		}
	},
	data() {
		return {
			selectedShapeName: '',
			selectedNode: undefined,
			nodeConfig: {
				nodes: [],
				centeredScaling: true,
			}
		}
	},
	methods: {
		DbUpdate: function(selectedNode) {
			const attrs = selectedNode.attrs;
			const typeId = attrs.typeId;
			const uniqueId = attrs.uniqueId;
			const x = selectedNode.getX();
			const y = selectedNode.getY();
			const width = selectedNode.width();
			const height = selectedNode.height();
			const scaleX = selectedNode.scaleX();
			const scaleY = selectedNode.scaleY();
			const rotation = selectedNode.rotation();

			try {
				axios.post(`/api/board/${typeId}/update/${uniqueId}`,
				{
					x: x,
					y: y,
					width: width,
					height: height,
					scaleX: scaleX,
					scaleY: scaleY,
					rotation: rotation,
				},
				{
					headers: {
						'Content-Type': 'application/json'
					}
				}).then((response) => {
					console.log(response.data);
				});
			} catch(ex) {
				console.error(ex);
			}
		},
		NodeRemove: function() {
			if (this.selectedNode == undefined) return;

			try {
				if (this.selectedNode.GetVueComponent != undefined) {
					const component = this.selectedNode.GetVueComponent()
					if (typeof component.Delete == 'function') component.Delete();
				}

				this.StopTransform();
			} catch(e) {
				console.error(e);
			}
		},
		StopTransform: function() {
			if (this.selectedNode != undefined) {
				this.DbUpdate(this.selectedNode);
				this.selectedNode.isEditMode = false;
			}
		
			this.selectedShapeName = '';
			this.selectedNode = undefined;
			this.UpdateTransformer();
		},
		StartTransformer: function(e) {
			if (e.target === e.target.getStage()) {
				this.StopTransform();
				return;
			}

			const clickedOnTransformer = e.target.getParent().className === 'Transformer';
			if (clickedOnTransformer) {
				return;
			}

			const name = e.target.name();
			this.selectedShapeName = name;
			this.UpdateTransformer();
		},
		UpdateTransformer: function() {
			const transformerNode = this.konvaNode;
			const stage = transformerNode.getStage();
			const { selectedShapeName } = this;

			const selectedNode = stage.findOne(`.${selectedShapeName}`);
			if (selectedNode === transformerNode.node()) {
				return;
			}

			if (selectedNode) {
				transformerNode.nodes([selectedNode]);
				this.selectedNode = selectedNode;
				this.selectedNode.isEditMode = true;
			} else {
				transformerNode.nodes([]);
				this.selectedNode = undefined;
			}

			transformerNode.getLayer().batchDraw();
		},
	}
}
</script>