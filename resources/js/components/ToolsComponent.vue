<template>
	<div
		class="container-fluid"
		@mouseover="handleMouseOver"
		@mouseleave="handleMouseLeave">
		<div
			class="tools row parent"
			:class="[hasFocusToolMenu ? 'tools-open' : 'tools-close']">
			<div class="col-xs-12 col-sm-12 col-md-8">
				<a @mousedown="addEraser" v-bind:class="getButtonClass('eraser')">Ластик</a>
				<a @mousedown="addPaint" v-bind:class="getButtonClass('paint')">Кисть</a>
				<!-- <a @mousedown="addNote" v-bind:class="getButtonClass('note')">Заметка</a> -->
				<a @mousedown="addText" v-bind:class="getButtonClass('text')">Текст</a>
				<!-- <a @mousedown="addArrow" v-bind:class="getButtonClass('arrow')">Стрелка</a> -->
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			hasFocusToolMenu: false,
			currentTool: undefined,
		}
	},
	methods: {
		getButtonClass(toolName) {
			return this.currentTool != toolName ? 'btn btn-primary' : 'btn btn-success';
		},
		resetTool() {
			this.currentToo = undefined;
			this.$emit('selecttool', this.currentTool);
		},
		selectTool(toolName, hold = false)  {
			if (this.currentTool == toolName)
				this.currentTool = undefined;
			else
				this.currentTool = toolName;

			this.$emit('selecttool', this.currentTool);

			if (!hold)
				this.currentTool = undefined;
		},
		addEraser() {
			this.selectTool('eraser', true);
		},
		addNote() {
			this.selectTool('note');
		},
		addPaint() {
			this.selectTool('paint', true);
		},
		addText() {
			this.selectTool('text');
		},
		addArrow() {
			this.selectTool('arrow');
		},
		handleMouseOver() {
			this.hasFocusToolMenu = true;
		},
		handleMouseLeave() {
			this.hasFocusToolMenu = false;
		}
	}
}
</script>

<style>
	.tools {
		position: fixed;
		left: 10;
		height: 50%;
		top: calc(50% / 2);
		background-color: rgba(173, 173, 173, 0.7);
		border-radius: 20px;
		z-index: 1;
	}

	.tools-open {
		width: 120px;
		transition: width .1s;
	}

	.tools-close {
		width: 50px;
		transition: width .15s;
	}
	
	.tools > div {
		text-align: center;
		width: 100%;
		height:100%;
		overflow-y: auto; 
	}

	.tools > div::-webkit-scrollbar { 
		display: none;
	}

	.tools-open > div > a {
		width: 100%;
		font-size: 10pt;
		margin-top: 5px;
		margin-bottom: 5px;
		left: auto;
		right: auto;

		transition: width .5s;
		transition: font-size .1s;
	}

	.tools-close > div > a {
		width: 10%;
		font-size: 0pt;
		margin-top: 5px;
		margin-bottom: 5px;
		left: auto;
		right: auto;

		transition: width .5s;
		transition: font-size .1s;
	}
</style>