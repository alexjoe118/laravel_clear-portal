export default class {
	constructor ( element ) {
		this.element = element;
		this.loanProduct = this.element.querySelector( '.js-loan-product' );
		this.requiredDocuments = this.element.querySelectorAll( '.js-required-documents' );
		this.interestInWCO = this.element.querySelector( '.js-interest-in-wco' );
	}

	updateFields () {
		this.interestInWCO.hidden =
		this.interestInWCO.disabled =
		[ '1', '2' ].includes( this.loanProduct.value );
	}

	updateRequiredDocuments () {
		this.requiredDocuments.forEach( requiredDocuments => {
			requiredDocuments.hidden =
			requiredDocuments.disabled =
			requiredDocuments.dataset.loanProduct !== this.loanProduct.value;
		});
	}

	setup () {
		this.loanProduct.addEventListener( 'change', () => {
			this.updateRequiredDocuments();
			this.updateFields();
		});

		this.updateRequiredDocuments();
	}
}
