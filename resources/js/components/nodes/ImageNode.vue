<template>
	<v-image ref="root" :config="nodeConfig" />
</template>

<script>
import SuperGif from 'libgif/libgif';

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
					document.body.appendChild(image);
					this.GifLoader(image);
				} else {
					this.nodeConfig.image = image;
				}
			};

			image.src = imageAddress;
			image.style.visibility = "hidden";
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
				this.$notify({
					title: `Удаление картинки: ${this.id}`,
					text: 'Картинка удалена',
					type: 'success'
				});
			}).catch((error) => {
				this.$notify({
					title: `Удаление картинки: ${this.id}`,
					text: 'Не удалось удалить картинку',
					type: 'error'
				});
			});
		},
		GifLoader: function(templateImage) {
			const konvaNode = this.konvaNode;
			if (konvaNode == undefined) return;

			const layer = konvaNode.getLayer();
			if (layer == undefined) return;

			const gif = new SuperGif({
				gif: templateImage,
				progressbar_height: 100,
				auto_play: true,
				loop_mode: true,
				draw_while_loading: true,
			});

			gif.load();

			const gif_canvas = gif.get_canvas();
			const div_root = gif_canvas.parentNode;
			const canvas = gif_canvas.cloneNode();
			const ctx = canvas.getContext('2d');

			this.gifCanvas = canvas;
			this.nodeConfig.image = canvas;

			function anim(t) {
					ctx.clearRect(0, 0, canvas.width, canvas.height);
					ctx.drawImage(gif_canvas, 0, 0);
					layer.draw();
					requestAnimationFrame(anim);
			};

			anim();

			try {
				document.body.removeChild(div_root);
			} catch(e) {
				console.error(e);
			}
		},
	}
}
</script>

<style>

</style>