<template>
	<div @drop.prevent="DropFileHandler" @dragenter.prevent @dragover.prevent>
		<board-tools />
		<board-zoom />
		<v-stage
			ref="boardStage"
			:config="boardStageConfig"
			@dragend="DragEndHandler"
			@dblclick="DoubleClickHandler"
			@mousedown="MouseDownHandler"
			@touchstart="TouchStartHandler">
			<v-layer ref="boardStageLayer">
				<board-text-bootstrap />
				<board-image-bootstrap />

				<board-transformer ref="transformer" />
			</v-layer>
		</v-stage>
	</div>
</template>


<script>
import { ImageUploader } from './ImageUploader.js';

export default {
	computed: {
		konvaStage() {
			return this.$refs.boardStage.getStage();
		},
		konvaStageLayer() {
			return this.$refs.boardStageLayer.getNode();
		},
		transformer() {
			return this.$refs.transformer;
		},
	},
	data() {
		return {
			boardStageConfig: {
				width: window.innerWidth,
				height: window.innerHeight,
				x: 0,
				y: 0,
				draggable: true,
			}
		}
	},
	created() {
		Vue.prototype.$boardInstance = this;
	},
	mounted() {
		Vue.prototype.$konvaTransformer = this.transformer;
		this.SetupScreenLocation();
	},
	methods: {
		DragEndHandler: function(e) {
			this.UpdateScreenLocation(e);
    },
		TouchStartHandler: function(e) {
			this.TransformerHandler(e);
		},
		MouseDownHandler: function(e) {
			this.TransformerHandler(e);
		},
		TransformerHandler: function(e) {
			this.transformer.StartTransformer(e);
		},
		DropFileHandler: function(e) {
			this.UploadImage(e);
		},
		DoubleClickHandler: function(e) {
			// this.NodeRemove();
		},
		// NodeRemove: function() {
		// 	this.transformer.NodeRemove();
		// },
		UploadImage: function(e) {
			const file = e.dataTransfer.files[0];
			const uploader = new ImageUploader(this.konvaStage);
			uploader.Upload(file, (response) => {
				const entry = response.data;
				if (entry == undefined) return;
				console.log(entry);
			});
		},
		SetupScreenLocation: function() {
			let urlHash = window.location.hash.replace('#', '');
			let positions = urlHash.split(':');

			if (positions.length >= 2 && !isNaN(positions[0]) && !isNaN(positions[1])) {
				this.boardStageConfig.x = parseInt(positions[0]);
				this.boardStageConfig.y = parseInt(positions[1]);
			}
		},
		UpdateScreenLocation: function(e) {
			if (e.target != this.konvaStage) return;
			const x = e.target.getX();
			const y = e.target.getY();
			window.location.hash =parseInt(x) + ':' + parseInt(y);
		}
	}
}
</script>

<style>

</style>