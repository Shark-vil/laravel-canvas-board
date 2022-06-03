<template>
	<v-layer ref="layer">
		<div v-for="entry in boardImages" :key="entry.id">
			<board-image
				:id="entry.id"
				:url="'storage/' + entry.path"
				:x="entry.x"
				:y="entry.y"
				:scaleX="entry.scaleX"
				:scaleY="entry.scaleY"
				ref="board-images"/>
		</div>
	</v-layer>
</template>

<script>
export default {
  data() {
    return {
			layer: undefined,
			editableImage: undefined,
			boardImages: [],
    };
  },
	mounted() {
		this.layer = this.$refs.layer;
		this.taskUpateDataList();
		this.taskUpdateElementsData();
  },
	methods: {
		taskUpateDataList: async function() {
			while (true)
			{
				try {
					await axios.get('/api/board/get').then(response => {
						const entries = response.data;
						if (entries == undefined) return;
						this.boardImages = entries;
					});
				} catch(ex) {
					console.error(ex);
				}

				await this.asyncDelay(1000);
			}
		},
		taskUpdateElementsData: async function() {
			while (true)
			{
				try {
					const entries = this.$refs['board-images'];

					if (entries != undefined) {
						const editableImage = this.editableImage;

						for (let index = 0; index < entries.length; index++) {
							const entry = entries[index];

							if (!entry.isVisible() || entry == editableImage)
								continue;
		
							if (entry.layer == undefined)
								entry.setLayer(this.layer);

							await entry.checkImage();
							// await this.asyncDelay(100);
						}
					}
				} catch(ex) {
					console.error(ex);
				}

				await this.asyncDelay(500);
			}
		},
		asyncDelay: async function(ms) {
			return await new Promise(resolve => setTimeout(resolve, ms));
		},
		onEdit: function(name) {
			let editableImage = undefined;

			try {
				const entries = this.$refs['board-images'];
				if (entries == undefined) return;
				for (let index = 0; index < entries.length; index++) {
					const entry = entries[index];
					if (entry.imageConfig.name == name) {
						editableImage = entry;
						break;
					}
				}
			} catch(ex) {
				console.error(ex);
			}

			this.editableImage = editableImage;
		},
		onDelete: function(name) {
			try {
				const entries = this.$refs['board-images'];
				if (entries == undefined) return;
				for (let index = 0; index < entries.length; index++) {
					const entry = entries[index];
					if (entry.imageConfig.name == name) {
						entry.deleteImage();
						entries.splice(index, 1)
						break;
					}
				}
			} catch(ex) {
				console.error(ex);
			}
		}
  }
};
</script>