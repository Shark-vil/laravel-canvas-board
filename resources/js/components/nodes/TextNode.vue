<template>
	<v-text ref="root" :config="nodeConfig" />
</template>

<script>
import { Task } from '../task';

export default {
	props: [
		'id',
		'typeId',
		'name',
		'text',
		'width',
		'height',
		'x',
		'y',
		'rotation',
		'fontSize',
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
			nodeConfig: {
				uniqueId: -1,
				typeId: '',
				name: '',
				text: '',
				scaleX: 1,
				scaleY: 1,
				x: 0,
				y: 0,
				fontSize: 12,
				rotation: 0,
				draggable: true,
				align: 'center',
				wrap: "word",
				verticalAlign: 'middle',
			}
		}
	},
	created() {
		const nodeConfig = this.nodeConfig;
		nodeConfig.name = this.name;
		nodeConfig.typeId = this.typeId;
		nodeConfig.uniqueId = parseInt(this.id);

		if (this.text != undefined) nodeConfig.text = this.text;
		// if (this.width != undefined) nodeConfig.width = parseFloat(this.width);
		if (this.scaleX != undefined) nodeConfig.scaleX = parseFloat(this.scaleX);
		if (this.scaleY != undefined) nodeConfig.scaleY = parseFloat(this.scaleY);
		if (this.x != undefined) nodeConfig.x = parseInt(this.x);
		if (this.y != undefined) nodeConfig.y = parseInt(this.y);
		if (this.fontSize != undefined) nodeConfig.fontSize = parseInt(this.fontSize);
		if (this.rotation != undefined) nodeConfig.rotation = parseFloat(this.rotation);
	},
	mounted() {
		this.konvaNode.on('dblclick dbltap', this.EditMode);
	},
	methods: {
		Modify: function(entry) {
			const konvaNode = this.konvaNode;

			konvaNode.text(entry.property.text);

			if (entry.property.fontSize != undefined)
				konvaNode.fontSize(parseInt(entry.property.fontSize));

			konvaNode.x(entry.x);
			konvaNode.y(entry.y);
			konvaNode.scaleX(entry.scaleX);
			konvaNode.scaleY(entry.scaleY);
			konvaNode.rotation(entry.rotation);
		},
		DbUpdate: function(text) {
			new Task(async () => {
				try {
					await axios.post('/api/board/text/update/' + this.id, { text: text },
					{
						headers: { 'Content-Type': 'application/json' }
					}).then((response) => {
						console.log('/api/board/text/update/' + this.id, response);
					});
				} catch(ex) {
					console.error(ex);
				}
			}).Start()
		},
		Delete: async function() {
			await axios.post('/api/board/text/delete/' + this.id).then(response => {
				console.log('/api/board/text/delete/' + this.id, response);
			});
		},
		EditMode: function () {
			const DbUpdate = this.DbUpdate;
			const textNode = this.konvaNode;
			textNode.isEditMode = true;
			textNode.hide();

			if (this.$konvaTransformer.selectedNode == textNode) {
				this.$konvaTransformer.StopTransform();
			}

			let textarea = this.CreateTextArea(textNode);

			let removeTextarea = function() {
				textarea.parentNode.removeChild(textarea);
				window.removeEventListener('click', handleOutsideClick);
				textNode.show();
				textNode.isEditMode = false;
			}

			// let setTextareaWidth = function(newWidth) {
			// 	if (!newWidth) newWidth = textNode.placeholder.length * textNode.fontSize();

			// 	const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
			// 	const isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
			// 	const isEdge = document.documentMode || /Edge/.test(navigator.userAgent);

			// 	if (isSafari || isFirefox) newWidth = Math.ceil(newWidth);
			// 	if (isEdge) newWidth += 1;

			// 	textarea.style.width = newWidth + 'px';
			// }

			textarea.addEventListener('keydown', function (e) {
				const isNotShiftEnter = e.enter && !e.shiftKey;
				const isEscape = e.escape;

				if (isNotShiftEnter) textNode.text(textarea.value);
				if (isNotShiftEnter || isEscape) removeTextarea();
			});

			textarea.addEventListener('keydown', function (e) {
				// const scale = textNode.getAbsoluteScale().x;
				// setTextareaWidth(textNode.width() * scale);
				textarea.style.height = 'auto';
				textarea.style.height = textarea.scrollHeight + textNode.fontSize() + 'px';
			});

			let handleOutsideClick = function(e) {
				if (e.target !== textarea) {
					textNode.text(textarea.value);
					DbUpdate(textarea.value);
					removeTextarea();
				}
			}

			setTimeout(() => window.addEventListener('click', handleOutsideClick));
		},
		CreateTextArea: function(textNode) {
			const textPosition = textNode.absolutePosition();
			const stage = textNode.getStage();
			const areaPosition = {
				x: stage.container().offsetLeft + textPosition.x,
				y: stage.container().offsetTop + textPosition.y,
			};

			let textarea = document.createElement('textarea');
			document.body.appendChild(textarea);
			textarea.value = textNode.text();
			textarea.style.position = 'absolute';
			textarea.style.top = areaPosition.y + 'px';
			textarea.style.left = areaPosition.x + 'px';
			textarea.style.width = textNode.width() - textNode.padding() * 2 + 'px';
			// textarea.style.height = textNode.height() - textNode.padding() * 2 + 5 + 'px';
			textarea.style.fontSize = textNode.fontSize() + 'px';
			textarea.style.border = 'none';
			textarea.style.padding = '0px';
			textarea.style.margin = '0px';
			textarea.style.overflow = 'hidden';
			textarea.style.background = 'none';
			textarea.style.outline = 'none';
			// textarea.style.resize = 'none';
			textarea.style.resize = 'both';
			textarea.style.lineHeight = textNode.lineHeight();
			textarea.style.fontFamily = textNode.fontFamily();
			textarea.style.transformOrigin = 'left top';
			textarea.style.textAlign = textNode.align();
			textarea.style.color = textNode.fill();

			textarea.style.height = 'auto';
			textarea.style.height = textarea.scrollHeight + 3 + 'px';

			textarea.focus();

			return textarea;
		}
	}
}
</script>