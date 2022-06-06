<template>
	<div>
		<board-text v-for="entry in dataList" :key="entry.id"
			:id="entry.id"
			:name="entry.uuid"
			:typeId="entry.type"
			:x="entry.x"
			:y="entry.y"
			:width="entry.width"
			:height="entry.height"
			:scaleX="entry.scaleX"
			:scaleY="entry.scaleY"
			:rotation="entry.rotation"
			:text="entry.property.text"
			:fontSize="entry.property.fontSize"
			ref="vueNodes" />
	</div>
</template>

<script>
import { Task } from '../task';
import DataEditor from '../dataEditor';

export default {
	data() {
		return  {
			dataList: [],
		}
	},
	mounted() {
		this.SyncCollection();
	},
	methods: {
		SyncCollection: async function() {
			const t = new Task();

			while(true) {
				try {
					await axios.get('/api/board/text/get').then((response) => {
						const entries = response.data;
						if (entries == undefined) return;
						this.dataList = entries;
						this.StartLinker();
					});
				} catch(ex) {
					console.error(ex);
				}

				await DataEditor.Modify(this.dataList, this.$refs.vueNodes);
				await t.Wait(1000);
			}
		},
		StartLinker: function() {
			const vueNodes = this.$refs.vueNodes;
			if (vueNodes == undefined) return;

			for (let index = 0; index < vueNodes.length; index++) {
				const vueNode = vueNodes[index];
				if (vueNode == undefined || vueNode.konvaNode == undefined) continue;
				if (vueNode.konvaNode.GetVueComponent == undefined) {
					vueNode.konvaNode.GetVueComponent = () => { return vueNode };
				}
			}
		}
	}
}
</script>