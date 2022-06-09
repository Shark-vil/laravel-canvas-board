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
		const MIX_PUSHER_APP_BROADCASTER = process.env.MIX_PUSHER_APP_BROADCASTER;
		const MIX_PUSHER_APP_KEY = process.env.MIX_PUSHER_APP_KEY;
		const MIX_PUSHER_APP_CLUSTER = process.env.MIX_PUSHER_APP_CLUSTER;
		const MIX_PUSHER_APP_ENCRYPTED = process.env.MIX_PUSHER_APP_ENCRYPTED;
		const MIX_PUSHER_APP_WS_HOST = process.env.MIX_PUSHER_APP_WS_HOST;
		const MIX_PUSHER_APP_WS_PATH = process.env.MIX_PUSHER_APP_WS_PATH;
		const MIX_PUSHER_APP_WS_PORT = process.env.MIX_PUSHER_APP_WS_PORT;
		const MIX_PUSHER_APP_WSS_PORT = process.env.MIX_PUSHER_APP_WSS_PORT;
		const MIX_PUSHER_APP_FORCE_TLS = process.env.MIX_PUSHER_APP_FORCE_TLS;
		const MIX_PUSHER_APP_DISABLE_STATS = process.env.MIX_PUSHER_APP_DISABLE_STATS;

		window.Echo = new Echo({
			broadcaster: MIX_PUSHER_APP_BROADCASTER != undefined ? MIX_PUSHER_APP_BROADCASTER : 'pusher',
			key: MIX_PUSHER_APP_KEY,
			cluster: MIX_PUSHER_APP_CLUSTER,
			encrypted: MIX_PUSHER_APP_ENCRYPTED == 'true' ? true : false,
			wsHost: MIX_PUSHER_APP_WS_HOST != undefined ? MIX_PUSHER_APP_WS_HOST : window.location.host,
			wsPath: MIX_PUSHER_APP_WS_PATH,
			wsPort: parseInt(MIX_PUSHER_APP_WS_PORT),
			wssPort: parseInt(MIX_PUSHER_APP_WSS_PORT),
			forceTLS: MIX_PUSHER_APP_FORCE_TLS == 'true' ? true : false,
			disableStats: MIX_PUSHER_APP_DISABLE_STATS == 'true' ? true : false,
			enabledTransports: ['ws', 'wss'],
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
		overflow: hidden;
	}

	body::-webkit-scrollbar { 
		display: none;
	}
</style>