import axios from 'axios';

export default class {
	constructor ( page ) {
		this.pageElement = page;
		this.downloadForms = this.pageElement.querySelectorAll( '.js-download' );
		this.groupSelects = this.pageElement.querySelectorAll( '.js-group-select' );
	}

	setup () {

		// Update the document group on change.
		this.groupSelects.forEach( select => {
			select.addEventListener( 'change', () => {
				select.parentElement.submit();
			});
		});
	}
}
