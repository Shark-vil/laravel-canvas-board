<template>
	<div>
		<board-image v-for="entry in dataList" :key="entry.id"
			:id="entry.id"
			:name="entry.uuid"
			:typeId="entry.type"
			:url="entry.property.path"
			:x="entry.x"
			:y="entry.y"
			:width="entry.width"
			:height="entry.height"
			:scaleX="entry.scaleX"
			:scaleY="entry.scaleY"
			:rotation="entry.rotation"
			ref="vueNodes" />
	</div>
</template>

<script>
import { NodeBootstrap } from './base/NodeBootstrap';

export default {
	data() {
		return  {
			dataList: [],
		}
	},
	mounted() {
		const bootstrap = new NodeBootstrap(this, 'image');
	},
}
</script>