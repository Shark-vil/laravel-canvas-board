<template>
	<div
		@drop.prevent="DropFileHandler"
		@dragenter.prevent
		@dragover.prevent>
		<board-tools ref="boardTools" @selecttool="SelectToolHandler" />
		<board-zoom />
		<v-stage
			ref="boardStage"
			:config="boardStageConfig"
			@dragend="DragEndHandler"
			@dblclick="DoubleClickHandler"
			@mouseup="MouseUpHandler"
			@mousemove="MouseMoveHandler"
			@mousedown="MouseDownHandler"
			@touchstart="TouchStartHandler">
			<v-layer ref="boardStageLayer">
				<board-text-bootstrap />
				<board-image-bootstrap />
				<board-paint-bootstrap ref="paintBootstrap" />

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
			eraser: false,
			eraserHasDown: false,
			eraserHasRemoved: false,
			boardStageConfig: {
				width: window.innerWidth,
				height: window.innerHeight,
				x: 0,
				y: 0,
				draggable: true,
				toolName: undefined,
				oldToolName: undefined,
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
		GetAddNewElementForm: function() {
			const stagePosition = this.konvaStage.getRelativePointerPosition();

			let posX =  stagePosition.x;
			let posY = stagePosition.y;
			let scaleX = 1;
			let scaleY = 1;

			const formData = new FormData();
			formData.append('x', posX);
			formData.append('y', posY);
			formData.append('scaleX', scaleX);
			formData.append('scaleY', scaleY);
			formData.append('rotation', 0);

			return formData;
		},
		SelectToolActions: function(selectType = undefined) {
			let formData = undefined;
			let elementName = undefined;
			let sendUrl = undefined;

			switch (this.toolName) {
				case 'eraser':
					this.eraser = true;
					this.transformer.StopTransform();
					this.boardStageConfig.draggable = false;
					this.konvaStage.container().style.cursor = 'not-allowed';
				break;

				case 'text':
					if (selectType == 'drag') {
						elementName = 'текст';
						sendUrl = '/api/board/text/add';
						formData = this.GetAddNewElementForm();
						formData.append('text', 'Example text');
						formData.append('fontSize', 12);
						formData.append('width', 200);
						this.toolName = undefined;
						this.konvaStage.container().style.cursor = 'default';
					} else {
						this.konvaStage.container().style.cursor = 'grab';
					}
				break;

				case 'paint':
					if (!this.$refs.paintBootstrap.hasPaint) {
						this.boardStageConfig.draggable = false;
						this.konvaStage.container().style.cursor = 'crosshair';
					}
					break;

				case undefined:
					this.boardStageConfig.draggable = true;
					this.eraser = false;
					this.konvaStage.container().style.cursor = 'default';
					break;
			
				default:
					break;
			}

			if (formData != undefined && sendUrl != undefined) {
				axios.post(sendUrl, formData,
				{
					headers: { 'Content-Type': 'multipart/form-data' }
				}).catch((error) => {
					this.$notify({
						title: `Не удалось добавить ${elementName}`,
						text: error,
						type: 'error'
					});
				});
			}
		},
		SelectToolHandler: function(toolName) {
			this.oldToolName = this.toolName;
			this.toolName = toolName;
			this.SelectToolActions();
		},
		DragEndHandler: function(e) {
			this.UpdateScreenLocation(e);
    },
		TouchStartHandler: function(e) {
			this.TransformerHandler(e);
		},
		MouseUpHandler: function(e) {
			if (this.eraser) {
				this.eraserHasDown = false;
				return;
			}

			this.$refs.paintBootstrap.MouseUpHandler(e);
			this.SelectToolActions('drag');

			if (this.toolName == 'paint') {
				this.$refs.paintBootstrap.StopPaint();
				this.boardStageConfig.draggable = true;
			}
		},
		MouseMoveHandler: async function(e) {
			if (this.eraser) {
				if (this.eraserHasDown && !this.eraserHasRemoved && e.target !== e.target.getStage()) {
					const shapeName = e.target.name();
					const selectedNode = this.konvaStage.findOne(`.${shapeName}`);

					if (selectedNode) {
						try {
							if (selectedNode.GetVueComponent != undefined) {
								const component = selectedNode.GetVueComponent()
								if (!this.eraserHasRemoved && typeof component.Delete == 'function') {
									this.eraserHasRemoved = true;
									await component.Delete();
									this.eraserHasRemoved = false;
								}
							}
						} catch { }
					}
				}
				return;
			}
			this.$refs.paintBootstrap.MouseMoveHandler(e);
		},
		MouseDownHandler: function(e) {
			if (this.eraser) {
				this.eraserHasDown = true;
				return;
			}

			if (this.toolName == 'paint') {
				this.boardStageConfig.draggable = false;
				this.$refs.paintBootstrap.StartPaint();
			}

			this.TransformerHandler(e);
			this.$refs.paintBootstrap.MouseDownHandler(e);
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