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
	props: [ 'id', 'url', 'x', 'y', 'width', 'height', 'scaleX', 'scaleY' ],
  data() {
    return {
			database: {
				id: -1,
			},
			isEdit: false,
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

		if (this.width != undefined && !isNaN(this.width))
			this.imageConfig.width = parseFloat(this.width);

		if (this.height != undefined && !isNaN(this.height))
			this.imageConfig.height = parseFloat(this.height);

		if (this.x != undefined && !isNaN(this.x))
			this.imageConfig.x = parseFloat(this.x);

		if (this.y != undefined && !isNaN(this.y))
			this.imageConfig.y = parseFloat(this.y);

		if (this.scaleX != undefined && !isNaN(this.scaleX))
			this.imageConfig.scaleX = parseFloat(this.scaleX);

		if (this.scaleY != undefined && !isNaN(this.scaleY))
			this.imageConfig.scaleY = parseFloat(this.scaleY);

		if (this.rotation != undefined && !isNaN(this.rotation))
			this.imageConfig.rotation = parseFloat(this.rotation);

    const image = new window.Image();
    image.src = this.url;
    image.onload = () => {
			this.imageConfig.image = image;
			// this.handleInit();
			// setInterval(this.checkImage, 1000);
    };
  },
	methods: {
		// handleInit() {
		// 	this.$emit('init', this);
		// },
		handleTransformEnd(e) {
			this.updateImage(e.target);
    },
    handleDragEnd(e) {
			this.updateImage(e.target);
    },
		isVisible() {
			if (!this.$refs.image.getNode().isClientRectOnScreen() || this.isEdit) return false;
			return true;
		},
		checkImage: async function() {
			if (!this.isVisible()) return;

			const imageConfig = this.imageConfig;

			await axios.get('/api/board/get/' + this.database.id).then(response => {
				const entry = response.data;
				imageConfig.x = entry.x;
				imageConfig.y = entry.y;
				imageConfig.scaleX = entry.scaleX;
				imageConfig.scaleY = entry.scaleY;
				imageConfig.width = entry.width;
				imageConfig.height = entry.height;
				imageConfig.rotation = entry.rotation;
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

			this.isEdit = true;

			await axios.post('/api/board/update/' + this.database.id, formData,
			{
				headers: {
					'Content-Type': 'multipart/form-data'
				}
			}).then(response => {
				let entry = response.data;
				console.log(entry);
			}).finally(() => {
				this.isEdit = false;
				console.log(this.isEdit);
			});
		}
  }
};
</script>