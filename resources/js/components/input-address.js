export default class {
	constructor ( element ) {
		this.element = element;
		this.formFields = element.closest( '.js-form-fields' );
	}

	setup () {
		if ( ! this.element ) return;

		// eslint-disable-next-line
		const autocomplete = new google.maps.places.Autocomplete( this.element , { types: [ 'address' ] });

		// Limit autocomplete to US.
		autocomplete.setComponentRestrictions({
			country: [ 'us' ]
		});

		// Listen to autocomplete update.
		autocomplete.addListener( 'place_changed', () => {
			const place = autocomplete.getPlace();

			if ( place && place.address_components ) {

				// Get the street name and number.
				let address = [];
				const parts = place.address_components;

				const number = parts.find( ({ types }) => types.includes( 'street_number' ) );
				if ( number ) address.push( number.long_name );

				const street = parts.find( ({ types }) => types.includes( 'route' ) );
				if ( street ) address.push( street.long_name );

				// Update the address input with the street name and number.
				this.element.value = address.join( ' ' );
				this.element.dispatchEvent( new Event( 'change' ) );

				// Autofill siblings "City", "State" and "Zip Code" fields when there is any.
				if ( this.formFields ) {
					const state = parts.find( ({ types }) => types.includes( 'administrative_area_level_1' ) );
					const stateInput = this.formFields.querySelector( 'select[name*="state"]' );
					const city = parts.find( ({ types }) => types.includes( 'locality' ) );
					const cityInput = this.formFields.querySelector( 'input[name*="city"]' );
					const zipCode = parts.find( ({ types }) => types.includes( 'postal_code' ) );
					const zipCodeInput = this.formFields.querySelector( 'input[name*="zip_code"]' );

					if ( state && stateInput ) {
						stateInput.value = state.short_name;
						stateInput.dispatchEvent( new Event( 'change' ) );
					}

					if ( city && cityInput ) {
						cityInput.value = city.long_name;
						cityInput.dispatchEvent( new Event( 'change' ) );
					}

					if ( zipCode && zipCodeInput ) {
						zipCodeInput.value = zipCode.long_name;
						zipCodeInput.dispatchEvent( new Event( 'change' ) );
					}
				}
			}
		});
	}
}
