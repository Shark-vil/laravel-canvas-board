<template>
	<div @drop.prevent="dragFile" @dragenter.prevent @dragover.prevent>
		<board-tools/>
		<v-stage
			ref="stage"
			:config="mainBoardConfig"
			@dragend="handleDragEnd"
			@dblclick="handleStageDoubleClick"
			@mousedown="handleStageMouseDown"
			@touchstart="handleStageMouseDown"
			@transformend="handleTransformEnd">
			<board-image-loader ref="image-laoder"/>
			<v-layer>
				<v-transformer ref="transformer"/>
			</v-layer>
		</v-stage>
	</div>
</template>

<script>
let width = window.innerWidth;
let height = window.innerHeight;

export default {
  data() {
    return {
			selectedShapeName: '',
      mainBoardConfig: {
        width: width,
        height: height,
				x: 0,
				y: 0,
				draggable: true,
      },
			closeButtonConfig: {
				radius: 25,
				fill: 'red'
			}
    };
  },
	mounted() {
		let urlHash = window.location.hash.replace('#', '');
		let positions = urlHash.split(':');
		if (positions.length == 2 && !isNaN(positions[0]) && !isNaN(positions[1])) {
			this.mainBoardConfig.x = parseInt(positions[0]);
			this.mainBoardConfig.y = parseInt(positions[1]);
		}
  },
	methods: {
		getStage() {
			return this.$refs.stage.getStage();
		},
		dragFile(e) {
			console.log('Files:' + e.dataTransfer.files);

			const file = e.dataTransfer.files[0];
			console.log('Single file: ' + file)

			const stage = this.getStage();
			const pos = stage.getRelativePointerPosition();

			const defaultSize = 250;
			const normalSize = 1 / defaultSize;

			var fr = new FileReader;

			fr.onload = function() {
				var img = new Image;

				img.onload = function() {
					let posX =  pos.x != null ? pos.x : 0;
					let posY = pos.y != null ? pos.y : 0;
					let scaleX = 1;
					let scaleY = 1;

					if (img.width > defaultSize) {
						scaleX  = (1 / img.width) / normalSize;
						scaleY = scaleX;
					} else if (img.height > defaultSize) {
						scaleY  = (1 / img.height) / normalSize;
						scaleX = scaleY;
					}

					let newWidth = scaleX * img.width;
					let newHeight = scaleY * img.height;

					console.log('W: ' + img.width + ', ' + newWidth);
					console.log('H: ' + img.height + ', ' + newHeight);
					console.log('X: ' + pos.x + ', Y: ' + pos.y);

					posX = posX - (newWidth / 2);
					posY = posY - (newHeight / 2);

					const formData = new FormData();
					formData.append('image', file);
					formData.append('x', posX);
					formData.append('y', posY);

					formData.append('scaleX', scaleX);
					formData.append('scaleY', scaleY);

					formData.append('width', img.width);
					formData.append('height', img.height);

					formData.append('rotation', 0);

					axios.post('/api/board/image/upload', formData,
					{
						headers: {
							'Content-Type': 'multipart/form-data'
						}
					}).then(response => {
						let entry = response.data;
						console.log(entry);
					});
					
					URL.revokeObjectURL(img.src);
				}

				img.src = fr.result;
			};

			fr.readAsDataURL(file);
		},
		handleTransformEnd() {
			this.selectedShapeName = '';
			this.updateTransformer();
		},
		handleStageDoubleClick(e) {
			if (this.selectedShapeName == undefined || this.selectedShapeName == '') return;

			try {
				this.$refs['image-laoder'].onDelete(e.target.name());
				this.selectedShapeName = '';
				this.updateTransformer();
			} catch(e) {
				console.error(e);
			}
		},
    handleStageMouseDown(e) {
      if (e.target === e.target.getStage()) {
        this.selectedShapeName = '';
        this.updateTransformer();
        return;
      }

      const clickedOnTransformer = e.target.getParent().className === "Transformer";
      if (clickedOnTransformer) {
        return;
      }

      const name =  e.target.name();
			this.selectedShapeName = name;

      this.updateTransformer();
    },
    updateTransformer() {
      const transformerNode = this.$refs.transformer.getNode();
      const stage = transformerNode.getStage();
      const { selectedShapeName } = this;

			this.$refs['image-laoder'].onEdit(selectedShapeName);

      const selectedNode = stage.findOne(`.${selectedShapeName}`);
      if (selectedNode === transformerNode.node()) {
        return;
      }

      if (selectedNode) {
        transformerNode.nodes([selectedNode]);
      } else {
        transformerNode.nodes([]);
      }

      transformerNode.getLayer().batchDraw();
    },
		handleDragEnd(e) {
			const xPos = e.target.getX();
			const yPos = e.target.getY()
			window.location.hash = '' + parseInt(xPos) + ':' + parseInt(yPos);
    },
  }
};
</script>

<style>
	body {
		overflow-x: hidden;
	}

	body::-webkit-scrollbar { 
		display: none;
	}
</style>