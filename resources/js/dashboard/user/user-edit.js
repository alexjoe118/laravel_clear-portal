export default class {
	constructor ( page ) {
		this.pageElement = page;
		this.form = this.pageElement.querySelector( '.js-form' );
		this.photoUpload = this.pageElement.querySelector( '.js-input-upload-image' );
	}

	setup () {

		// Submit the form when uploading a new photo.
		this.photoUpload.addEventListener( 'change', () => {
			this.form.submit();
		});
	}
}
