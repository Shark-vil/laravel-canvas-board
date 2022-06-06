<template>
	<v-image ref="root" :config="nodeConfig" />
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
		'url',
		'scaleX',
		'scaleY'
	],
	computed: {
		konvaNode() {
			return this.$refs.root.getNode();
		}
	},
	data() {
		return {
			gifCanvas: undefined,
			nodeConfig: {
				uniqueId: -1,
				typeId: '',
				name: '',
				text: '',
				image: '',
				width: 100,
				height: 100,
				scaleX: 1,
				scaleY: 1,
				x: 0,
				y: 0,
				rotation: 0,
				draggable: true,
			}
		}
	},
	created() {
		const nodeConfig = this.nodeConfig;
		nodeConfig.name = this.name;
		nodeConfig.typeId = this.typeId;
		nodeConfig.uniqueId = parseInt(this.id);

		if (this.width != undefined) nodeConfig.width = parseFloat(this.width);
		if (this.height != undefined) nodeConfig.height = parseFloat(this.height);
		if (this.scaleX != undefined) nodeConfig.scaleX = parseFloat(this.scaleX);
		if (this.scaleY != undefined) nodeConfig.scaleY = parseFloat(this.scaleY);
		if (this.x != undefined) nodeConfig.x = parseInt(this.x);
		if (this.y != undefined) nodeConfig.y = parseInt(this.y);
		if (this.rotation != undefined) nodeConfig.rotation = parseFloat(this.rotation);
	},
	mounted() {
		if (typeof this.url == 'string') {
			const imageAddress = window.APP_URL + '/storage' + this.url;
			const extension = imageAddress.split('.').pop();
			const image = new window.Image();

			image.onload = () => {
				if (extension == 'gif') {
					this.gifCanvas = document.createElement('canvas');
					this.nodeConfig.image = this.gifCanvas;
					gifler(imageAddress).frames(this.gifCanvas, this.onDrawGif);
				} else {
					this.nodeConfig.image = image;
				}
			};

			image.src = imageAddress;
		}
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
		Delete: async function() {
			await axios.post('/api/board/image/delete/' + this.id).then(response => {
				console.log('/api/board/image/delete/' + this.id, response);
			});
		},
	}
}
</script>

<style>

</style>