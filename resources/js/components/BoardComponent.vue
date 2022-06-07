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

		<notifications position="bottom right" />
	</div>
</template>


<script>
import Echo from 'laravel-echo';
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
		window.Echo = new Echo({
			broadcaster: process.env.MIX_PUSHER_APP_BROADCASTER,
			key: process.env.MIX_PUSHER_APP_KEY,
			cluster: process.env.MIX_PUSHER_APP_CLUSTER,
			wsHost: process.env.MIX_PUSHER_APP_WS_HOST,
			wsPort: parseInt(process.env.MIX_PUSHER_APP_WS_PORT),
			forceTLS: process.env.MIX_PUSHER_APP_FORCE_TLS == 'true' ? true : false,
			disableStats: process.env.MIX_PUSHER_APP_DISABLE_STATS == 'true' ? true : false,
		});

		Vue.prototype.$boardInstance = this;
	},
	mounted() {
		Vue.prototype.$konvaTransformer = this.transformer;
		this.SetupScreenLocation();

		window.Echo.join('board')
			.here((users) => {
				this.$notify({
					title: 'С подключением',
					text: 'Вы успешно подключились к серверу',
					type: 'success'
				});
			})
			.joining((user) => {
				this.$notify({
					title: 'Новый участник',
					text: `${user.name} присоединился к доске`,
					type: 'success'
				});
			})
			.leaving((user) => {
				this.$notify({
					title: 'Участник отключился',
					text: `${user.name} ушёл с доски`,
					type: 'warn'
				});
			})
			.error((error) => {
				this.$notify({
					title: 'Ошибка подключения',
					text: error,
					type: 'error'
				});
			});
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
				this.$notify({
					title: `Загрузка: ${file.name}`,
					text: 'Файл загружкен',
					type: 'success'
				});
			}, (error) => {
				this.$notify({
					title: `Загрузка: ${file.name}`,
					text: 'Ошибка загрузки файла',
					type: 'error'
				});
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
	body {
		overflow-x: hidden;
	}

	body::-webkit-scrollbar { 
		display: none;
	}
</style>