export default class {
	constructor ( element ) {
		this.element = element;
		this.input = this.element.querySelector( '.js-input' );
		this.uploadLimit = 24;
	}

	formatBytes ( bytes ) {
		if ( bytes === 0 ) return '0 bytes';

		const units = [ 'bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' ];
		const i = Math.floor( Math.log( bytes ) / Math.log( 1024 ) );

		return parseFloat( ( bytes / Math.pow( 1024, i ) ).toFixed( 2 ) ) + ' ' + units[i];
	}

	setup () {

		// If it's a form, submit it when the input changes.
		if ( this.element.tagName === 'FORM' ) {
			this.input.addEventListener( 'change', () => this.element.submit() );

		// If it's not a form, append the file when the input changes.
		} else {

			// Create a model div for uploaded files.
			const fileElement = this.element.querySelector( '.js-file' );
			const fileModel = fileElement.cloneNode( true );
			fileModel.classList.remove( 'existing' );
			fileModel.classList.remove( 'hidden' );

			if ( fileElement.classList.contains( 'hidden' ) ) {
				fileElement.remove();
			}

			// File buffer.
			const buffer = new DataTransfer();

			this.input.addEventListener( 'change', () => {

				// Bail early if nothing changed. It avoids document duplication.
				if ( buffer.files.length ) {
					const nothingChanged = [ ...this.input.files ].filter( ( file, index ) => {
						return file && file.size != [ ...buffer.files ][ index ].size;
					}).length === 0;

					if ( nothingChanged ) return;
				}

				// Clean up uploaded file if it's not a multiple upload input.
				if ( ! this.input.multiple ) {
					const existingFile = this.element.querySelector( '.js-file' );
					if ( existingFile ) existingFile.remove();
					buffer.clearData();
				}

				[ ...this.input.files ].forEach( fileData => {
					const file = fileModel.cloneNode( true );

					file.querySelector( '.js-file-name' ).textContent = fileData.name;
					file.querySelector( '.js-file-size' ).textContent = this.formatBytes( fileData.size );

					const removeButton = file.querySelector( '.js-file-remove' );

					if ( removeButton ) {
						file.querySelector( '.js-file-remove' ).onclick = () => {
							const removedIndex = [ ...this.element.querySelectorAll( '.js-file' ) ].indexOf( file );
							file.remove();

							buffer.items.remove( removedIndex );
							this.input.files = buffer.files;

							this.checkFieldValidity();
						};
					}

					buffer.items.add( fileData );
					this.element.append( file );
				});

				this.input.files = buffer.files;

				this.checkFieldValidity();
			});
		}
	}

	checkFieldValidity () {
		const fileSizes = this.sumFilesSizes();

		if ( fileSizes > this.uploadLimit ) {
			this.input.setCustomValidity( `Max allowed upload size is ${this.uploadLimit}mb` );

			return;
		}

		this.input.setCustomValidity( '' );
	}

	sumFilesSizes () {
		const convertToMB = bytes => {
			const sizeInKB = bytes / 1024;
			const sizeInMB = sizeInKB / 1024;
			return sizeInMB;
		};

		const sum = [ ...this.input.files ].reduce( ( accumulator, file ) => {
			return accumulator + file.size;
		}, 0 );

		return convertToMB( sum );
	}
}
