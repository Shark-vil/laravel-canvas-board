class ImageUploader {
	constructor(konvaStage) {
		this.konvaStage = konvaStage;
		console.log(this.konvaStage);
	}

	Upload(file, successCallback, errorCallback) {
		const konvaStage = this.konvaStage;
		// const stagePosition = konvaStage.getRelativePointerPosition();
		const defaultSize = 250;
		const normalSize = 1 / defaultSize;

		var fr = new FileReader;

		fr.onload = function() {
			var img = new Image;

			img.onload = function() {
				const stagePosition = konvaStage.getRelativePointerPosition();
				let posX =  stagePosition.x;
				let posY = stagePosition.y;
				let scaleX = 1;
				let scaleY = 1;

				if (img.width > defaultSize) {
					scaleX  = (1 / img.width) / normalSize;
					scaleY = scaleX;
				} else if (img.height > defaultSize) {
					scaleY  = (1 / img.height) / normalSize;
					scaleX = scaleY;
				}

				let newWidth = scaleX * img.width;
				let newHeight = scaleY * img.height;

				console.log('W: ' + img.width + ', ' + newWidth);
				console.log('H: ' + img.height + ', ' + newHeight);
				console.log('X: ' + stagePosition.x + ', Y: ' + stagePosition.y);

				// posX = posX + (konvaStage.width() / 2);
				// posY = posY + (konvaStage.height() / 2);

				posX -= newWidth / 2;
				posY -= newHeight / 2;

				const formData = new FormData();
				formData.append('image', file);
				formData.append('x', posX);
				formData.append('y', posY);
				formData.append('scaleX', scaleX);
				formData.append('scaleY', scaleY);
				formData.append('width', img.width);
				formData.append('height', img.height);
				formData.append('rotation', 0);

				axios.post('/api/board/image/add', formData,
				{
					headers: {
						'Content-Type': 'multipart/form-data'
					}
				}).then(response => {
					let entry = response.data;
					console.log(response);

					if (typeof successCallback == 'function') {
						successCallback(response);
					}
				}).catch(error => {
					if (typeof errorCallback == 'function') {
						errorCallback(error);
					}
				});
				
				URL.revokeObjectURL(img.src);
			}

			img.src = fr.result;
		};

		fr.readAsDataURL(file);
	}
}

module.exports.ImageUploader = ImageUploader