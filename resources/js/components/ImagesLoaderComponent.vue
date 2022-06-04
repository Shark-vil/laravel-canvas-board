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
				:rotation="entry.rotation"
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
  },
	methods: {
		taskUpateDataList: async function() {
			while (true)
			{
				const boardImages = this.$refs['board-images'];
				const editableImage = this.editableImage;
				// let isVisible = false;

				// if (boardImages != undefined && boardImages.length != 0) {
				// 	for (let boardEntryIndex = boardImages.length; boardEntryIndex >= 0; boardEntryIndex--) {
				// 		const boardEntry = boardImages[boardEntryIndex];
				// 		if (boardEntry == undefined) continue;
				// 		if (boardEntry.isVisible()) {
				// 			isVisible = true;
				// 			break;
				// 		}
				// 	}
				// } else {
				// 	isVisible = true;
				// }

				// if (!isVisible) {
				// 	await this.asyncDelay(500);
				// 	continue;
				// }

				try {
					await axios.get('/api/board/image/get').then(async (response) => {
						const entries = response.data;
						if (entries == undefined) return;

						if (boardImages != undefined) {
							for (let boardEntryIndex = boardImages.length; boardEntryIndex >= 0; boardEntryIndex--) {
								const boardEntry = boardImages[boardEntryIndex];
								if (boardEntry == undefined) continue;
		
								for (let entryIndex = entries.length; entryIndex >= 0; entryIndex--) {
									const entry = boardImages[entryIndex];
									if (entry != undefined && entry != editableImage && entry.id == boardEntry.id) {
										boardEntry.updateImageData(entry);

										if (boardEntry.layer == undefined) {
											boardEntry.setLayer(this.layer);
										}

										break;
									}

									// await this.asyncDelay(10);
								}
		
								// await this.asyncDelay(10);
							}
						}

						this.boardImages = entries;
					});
				} catch(ex) {
					console.error(ex);
				}

				await this.asyncDelay(1000);
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