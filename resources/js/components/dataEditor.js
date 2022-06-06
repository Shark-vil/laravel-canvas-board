const _ = require('./task');
const t = new _.Task();

export default {
	Modify: async function(entries, nodes) {
		if (entries == undefined || nodes == undefined) return;

		for (let entryIndex = 0; entryIndex < entries.length; entryIndex++) {
			const entry = entries[entryIndex];

			for (let nodeIndex = 0; nodeIndex < nodes.length; nodeIndex++) {
				const node = nodes[nodeIndex];
				
				if (entry.id == node.id) {
					if (!node.konvaNode || !node.konvaNode.isEditMode) {
						node.Modify(entry);
					}

					// console.log('Edit: ' + entry.id);

					await t.Wait(100);

					break;
				}
			}
		}
	}
}