class Task {
	constructor(func) {
		this.func = func;
		this.started = false;
	}

	async Wait(ms) {
		return await new Promise(resolve => setTimeout(resolve, ms));
	}

	async Yield() {
		return await this.Wait(10);
	}

	Start(...args) {
		this.Call(...args);
	}

	async Call(...args) {
		const func = this.func;

		if (func == undefined) {
			var err = new Error();
			console.error(err.stack);
			return;
		}

		this.started = true;
		await func(this, ...args);
		this.started = false;
	}
}

module.exports.Task = Task