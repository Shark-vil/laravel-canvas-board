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
import { NodeBootstrap } from './base/NodeBootstrap';

export default {
	data() {
		return  {
			dataList: [],
		}
	},
	mounted() {
		const bootstrap = new NodeBootstrap(this, 'text');
	}
}
</script>