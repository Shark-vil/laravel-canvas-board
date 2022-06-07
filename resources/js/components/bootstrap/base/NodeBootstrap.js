const _ = require('../../task');

class NodeBootstrap {
	constructor(vueComponent, nodeType) {
		this.self = vueComponent;
		this.type = nodeType;
		this.task = new _.Task();

		axios.get(`/api/board/${this.type}/get`).then((response) => {
			this.self.dataList = response.data;
			this.VueLinker();
		});

		window.Echo.channel(`nodes.created.${this.type}`).listen('NodeCreated', async (response) => {
			this.self.dataList.push(response.node);
			this.VueLinker();
		});

		window.Echo.channel(`nodes.removed.${this.type}`).listen('NodeRemoved', (response) => {
			this.self.dataList = this.self.dataList.filter(x =>x.id != response.id);
		});

		window.Echo.channel(`nodes.transform.${this.type}`).listen('NodeTransform', (response) => {
			const entry = response.node;

			const vueNodes = this.self.$refs.vueNodes;
			if (vueNodes == undefined) return;

			for (let index = 0; index < vueNodes.length; index++) {
				const vueNode = vueNodes[index];

				if (vueNode == undefined || vueNode.Modify == undefined || vueNode.nodeConfig.uniqueId != entry.id)
					continue;
				
				vueNode.Modify(entry);
			}
		});
	}

	async VueLinker() {
		await this.task.Wait(100);
		
		const self = this.self;
		const vueNodes = self.$refs.vueNodes;
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

module.exports.NodeBootstrap = NodeBootstrap;