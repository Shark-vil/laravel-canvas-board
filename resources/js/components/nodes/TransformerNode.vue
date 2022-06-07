<template>
	<div>
		<v-group @mousedown="CloseButtonClickHandler" :config="closeButtonGroupConfig" ref="closeButtonGroup">
			<v-circle :config="closeButtonConfig" ref="closeButton" />
			<v-text :config="closeButtonTextConfig" ref="closeButtonText" />
		</v-group>
		<v-transformer :config="nodeConfig" ref="root" />
	</div>
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
			},
			closeButtonGroupConfig: {
				visible: false,
			},
			closeButtonTextConfig: {
				text: 'X',
				x: 0,
				y: 0,
				fill: '#ffff',
				fontSize: 14,
				align: 'center',
			},
			closeButtonConfig: {
				x: 0,
				y: 0,
				radius: 8,
        fill: 'red',
        stroke: 'black',
        strokeWidth: 1,
			}
		}
	},
	created() {
		window.addEventListener('wheel', this.UpdateCloseButtonPosition);
	},
	destroyed() {
		window.removeEventListener('wheel', this.UpdateCloseButtonPosition);
	},
	mounted() {
		setTimeout(() => {
			const transformer = this.konvaNode;
			const closeGroup = this.$refs.closeButtonGroup.getNode();
			transformer.add(closeGroup);
			closeGroup.visible(true);
			transformer.on('transform', this.UpdateCloseButtonPosition);
		}, 100);
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
			}).catch((error) => {
				this.$notify({
					title: `Обновление позиции: ${uniqueId}`,
					text: `Не удалось обновить позицию элемента "${typeId}"`,
					type: 'error'
				});
			});
		},
		NodeRemove: async function() {
			if (this.selectedNode == undefined) return;

			try {
				if (this.selectedNode.GetVueComponent != undefined) {
					const component = this.selectedNode.GetVueComponent()
					if (typeof component.Delete == 'function') {
						await component.Delete();
						this.selectedNode = undefined;
					}
				}

				this.StopTransform();
			} catch(e) {
				console.error(e);
			}
		},
		CloseButtonClickHandler: function() {
			this.NodeRemove();
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

			if (this.selectedNode != undefined && this.selectedNode != e.target) {
				this.StopTransform();
			}

			const name = e.target.name();
			this.selectedShapeName = name;
			this.UpdateTransformer();
		},
		UpdateCloseButtonPosition: function() {
			const closeButton = this.$refs.closeButton.getNode();
			const closeButtonText = this.$refs.closeButtonText.getNode();
			// const width = this.konvaNode.width() / this.konvaNode.scaleX();
			// const heihgt = this.konvaNode.height() / this.konvaNode.scaleY();
			const width = this.konvaNode.width();
			const heihgt = this.konvaNode.height();
			const x = width / 2;
			const y = heihgt + closeButton.radius() + 15;

			closeButton.x(x);
			closeButton.y(y);

			closeButtonText.x(x - closeButtonText.width() / 2);
			closeButtonText.y(1 + (y - closeButtonText.height() / 2));
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
				this.UpdateCloseButtonPosition();
				// closeButton.x(this.konvaNode.width() + 10);
				// closeButton.y(-10);
			} else {
				transformerNode.nodes([]);
				this.selectedNode = undefined;
			}

			transformerNode.getLayer().batchDraw();
		},
	}
}
</script>