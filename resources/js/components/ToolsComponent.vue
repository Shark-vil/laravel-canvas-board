<template>
	<div
		class="container-fluid"
		@mouseover="handleMouseOver"
		@mouseleave="handleMouseLeave">
		<div
			class="tools row parent"
			:class="[hasFocusToolMenu ? 'tools-open' : 'tools-close']">
			<div class="col-xs-12 col-sm-12 col-md-8">
				<a @click="addNote" class="btn btn-primary">Заметка</a>
				<a @click="addText" class="btn btn-primary">Текст</a>
				<a @click="addArrow" class="btn btn-primary">Стрелка</a>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {
			hasFocusToolMenu: false,
		}
	},
	methods: {
		addNote() {

		},
		addText() {
			const board = this.$boardInstance;
			const konvaStage = board.konvaStage;
			
			let posX = -konvaStage.getX();
			let posY = -konvaStage.getY();

			// let scaleX = konvaStage.scaleX();
			// let scaleY = konvaStage.scaleY()

			// posX += (konvaStage.width() / scaleX) / 2;
			// posY += (konvaStage.height() / scaleY) / 2;

			posX += konvaStage.width() / 2;
			posY += konvaStage.height() / 2;

			const width = 200;

			const formData = new FormData();
			formData.append('text', 'Example text');
			formData.append('fontSize', 12);
			formData.append('x', posX - (width / 2));
			formData.append('y', posY);
			formData.append('scaleX', 1);
			formData.append('scaleY', 1);
			formData.append('width', width);
			formData.append('rotation', 0);

			axios.post('/api/board/text/add', formData,
			{
				headers: { 'Content-Type': 'multipart/form-data' }
			}).catch((error) => {
				this.$notify({
					title: 'Не удалось добавить текст',
					text: error,
					type: 'error'
				});
			});
		},
		addArrow() {

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