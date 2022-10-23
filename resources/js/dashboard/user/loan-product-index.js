export default class {
	constructor ( page ) {
		this.pageElement = page;
	}

	setup () {
		const modal = document.querySelector( '.js-modal' );
		const loanProductSelect = modal.querySelector( '[name="loan_product_id"]' );

		// Autopopulate the modal Loan Product.
		modal.addEventListener( 'open', ({ detail }) => {
			loanProductSelect.value = detail.trigger.dataset.loanProductId;
			loanProductSelect.dispatchEvent( new Event( 'change' ) );
		});
	}
}
