export default class {
	constructor ( page ) {
		this.pageElement = page;
		this.loanProductSelect = this.pageElement.querySelector( '.js-loan-product' );
		this.fields = {
			'term-length': [ 1, 2, 3, 4, 5, 6, 7, 8 ],
			'term-length-display': [ 1, 2, 3, 4, 5, 6, 7, 8 ],
			'maximum-amount': [ 1, 2, 3, 4, 5, 6, 7, 8 ],
			'prepayment-discount': [ 1, 2, 3, 4, 5, 6, 7, 8 ],
			'closing-costs': [ 1, 2, 3, 4, 5, 6, 7, 8 ],
			'closing-costs-display': [ 1, 2, 3, 4, 5, 6, 7, 8 ],
			'cost-of-capital': [ 1, 2 ],
			'cost-of-capital-display': [ 1, 2 ],
			'rate': [ 1, 2 ],
			'interest-rate': [ 3, 4, 5, 6, 7, 8 ],
			'draw-fee': [ 8 ],
			'multiplier': [ 8 ],
			'payment-frequency-1': [ 1, 2 ],
			'payment-frequency-2': [ 3, 4, 5, 6, 7 ],
			'payment-frequency-3': [ 8 ],
			'number-of-payments': [ 1, 2, 8 ],
			'total-credit-limit': [ 9 ],
			'factor-rate': [ 9 ],
			'maximum-advance': [ 9 ],
			'misc-fees': [ 9 ]
		};
	}

	filterFields () {
		const selected = Number( this.loanProductSelect.value );

		for ( const [ fieldClass, loanProducts ] of Object.entries( this.fields ) ) {

			// Hide/show the fields according to the Loan Product.
			const hasField = loanProducts.find( loanProduct => selected === loanProduct );
			const field = this.pageElement.querySelector( `.js-${fieldClass}` );
			field.hidden = ! hasField;
			field.disabled = ! hasField;
		}
	}

	setup () {

		// Update the fields when the loan product changes.
		this.loanProductSelect.addEventListener( 'change', () => this.filterFields() );
		this.filterFields();
	}
}
