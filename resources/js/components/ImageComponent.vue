<template>
	<v-image
		ref="image"
		:config="imageConfig"
		@dragend="handleDragEnd"
		@transformend="handleTransformEnd">
	</v-image>
</template>

<script>
export default {
	props: [ 'id', 'url', 'x', 'y', 'width', 'height', 'scaleX', 'scaleY', 'rotation' ],
  data() {
    return {
			gifCanvas: undefined,
			layer: undefined,
			database: {
				id: -1,
			},
			imageConfig: {
				name: '',
				image: null,
				x: 0,
				y: 0,
				scaleX: 1,
				scaleY: 1,
				rotation: 0,
				draggable: true,
			}
    };
  },
  created() {
		this.database.id = parseInt(this.id);
		this.imageConfig.name = 'img-' + this.database.id;

		this.imageConfig.width = this.width != undefined && !isNaN(this.width)
			? parseFloat(this.width) : this.imageConfig.width;

		this.imageConfig.height = this.height != undefined && !isNaN(this.height)
			? parseFloat(this.height) : this.imageConfig.height;

		this.imageConfig.x = this.x != undefined && !isNaN(this.x)
			? parseFloat(this.x) : this.imageConfig.x;

		this.imageConfig.y = this.y != undefined && !isNaN(this.y)
			? parseFloat(this.y) : this.imageConfig.y;

		this.imageConfig.scaleX = this.scaleX != undefined && !isNaN(this.scaleX)
			? parseFloat(this.scaleX) : this.imageConfig.scaleX;

		this.imageConfig.scaleY = this.scaleY != undefined && !isNaN(this.scaleY)
			? parseFloat(this.scaleY) : this.imageConfig.scaleY;

		this.imageConfig.rotation = this.rotation != undefined && !isNaN(this.rotation)
			? parseFloat(this.rotation) : this.imageConfig.rotation;

		const imageAddress = window.APP_URL + '/' + this.url;
		const extension = imageAddress.split('.').pop();
    const image = new window.Image();
    image.src = imageAddress;
    image.onload = () => {
			if (extension == 'gif') {
				this.gifCanvas = document.createElement('canvas');
				this.imageConfig.image = this.gifCanvas;
				gifler(imageAddress).frames(this.gifCanvas, this.onDrawGif);
			} else {
				this.imageConfig.image = image;
			}
    };
  },
	methods: {
		handleTransformEnd(e) {
			this.updateImage(e.target);
    },
    handleDragEnd(e) {
			this.updateImage(e.target);
    },
		isVisible() {
			if (!this.$refs.image.getNode().isClientRectOnScreen()) return false;
			return true;
		},
		updateImageData(entry) {
			const imageConfig = this.imageConfig;
			imageConfig.x = entry.x;
			imageConfig.y = entry.y;
			imageConfig.scaleX = entry.scaleX;
			imageConfig.scaleY = entry.scaleY;
			imageConfig.width = entry.width;
			imageConfig.height = entry.height;
			imageConfig.rotation = entry.rotation;
		},
		deleteImage: async function() {
			await axios.post('/api/board/delete/' + this.database.id).then(response => {
				let entry = response.data;
				console.log(entry);
			});
		},
		updateImage: async function(target) {
			const formData = new FormData();
			formData.append('x', target.getX());
			formData.append('y', target.getY());
			formData.append('width', target.getWidth());
			formData.append('height', target.getHeight());
			formData.append('scaleX', target.scaleX());
			formData.append('scaleY', target.scaleY());
			formData.append('rotation', target.rotation());

			await axios.post('/api/board/update/' + this.database.id, formData,
			{
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			}).then(response => {
				let entry = response.data;
				console.log(entry);
			});
		},
		setLayer(layer) {
			this.layer = layer;
		},
		onDrawGif(ctx, frame) {
			if (this.layer == undefined) return;
			this.gifCanvas.width = frame.width;
			this.gifCanvas.height = frame.height;
			ctx.drawImage(frame.buffer, 0, 0);
			this.layer.getNode().draw();
		}
  }
};
</script>