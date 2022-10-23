export default class {
	constructor ( page ) {
		this.pageElement = page;
		this.paymentFrequencySelect = this.pageElement.querySelector( '.js-payment-frequency' );
		this.paymentDaySelects = {
			'monthly': this.pageElement.querySelector( '.js-payment-day-monthly' ),
			'semi-monthly': this.pageElement.querySelector( '.js-payment-day-semi-monthly' ),
			'weekly': this.pageElement.querySelector( '.js-payment-day-weekly' )
		};
	}

	setup () {

		// Update disabled the Payment Day field if the Payment Frequency is "daily".
		this.paymentFrequencySelect.addEventListener( 'change', () => {
			for ( const [ frequency, select ] of Object.entries( this.paymentDaySelects ) ) {
				select.hidden = select.disabled = frequency !== this.paymentFrequencySelect.value;
			}
		});
	}
}
