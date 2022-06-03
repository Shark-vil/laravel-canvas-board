<template>
	<div @drop.prevent="dragFile" @dragenter.prevent @dragover.prevent>
		<v-stage
			ref="stage"
			:config="mainBoardConfig"
			@dragend="handleDragEnd"
			@mousedown="handleStageMouseDown"
			@touchstart="handleStageMouseDown">
			<v-layer>
				<div v-for="entry in boardImages" :key="entry.id">
					<board-image
						:id="entry.id"
						:url="'storage/' + entry.path"
						:x="entry.x"
						:y="entry.y"
						:scaleX="entry.scaleX"
						:scaleY="entry.scaleY"
						ref="board-images"/>
				</div>
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
			boardImages: [],
      mainBoardConfig: {
        width: width,
        height: height,
				draggable: true,
      },
    };
  },
	mounted() {
		let urlHash = window.location.hash.replace('#', '');
		let positions = urlHash.split(':');
		if (positions.length == 2 && !isNaN(positions[0]) && !isNaN(positions[1])) {
			this.mainBoardConfig.x = parseInt(positions[0]);
			this.mainBoardConfig.y = parseInt(positions[1]);
		}

		this.runBackgroundTask();
  },
	methods: {
		runBackgroundTask: async function() {
			while (true)
			{
				try {
					await axios.get('/api/board/get').then(response => {
						const entries = response.data;
						this.boardImages = entries;
					});
				} catch(ex) {
					console.error(ex);
				}

				try {
					const entries = this.$refs['board-images']
					for (let index = 0; index < entries.length; index++) {
						const entry = entries[index];
						if (entry.isVisible()) {
							await entry.checkImage();
							await this.asyncDelay(100);
						}
					}
				} catch(ex) {
					console.error(ex);
				}

				await this.asyncDelay(500);
			}
		},
		asyncDelay: async function(ms) {
			return await new Promise(resolve => setTimeout(resolve, ms));
		},
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

			const boardImages = this.boardImages;

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

					axios.post('/api/board/upload', formData,
					{
						headers: {
							'Content-Type': 'multipart/form-data'
						}
					}).then(response => {
						let entry = response.data;
						boardImages.push(entry);
						console.log(entry);
					});
					
					URL.revokeObjectURL(img.src);
				}

				img.src = fr.result;
			};

			fr.readAsDataURL(file);
		},
    handleStageMouseDown(e) {
      if (e.target === e.target.getStage()) {
        this.selectedShapeName = "";
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