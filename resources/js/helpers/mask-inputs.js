import Inputmask from 'inputmask';

/**
 * List of input selectors and its respective masks.
 *
 * @var {object}
 */
const masks = {
	'phone-number': [ {
		mask: '(999) 999-9999[ 999]',
		removeMaskOnSubmit: true
	}, 'e.g. (123) 456-7890' ],

	'date': [ {
		mask: '99/99/9999'
	}, 'mm/dd/yyyy' ],

	'federal-tax-id': [ {
		mask: '99-9999999',
		removeMaskOnSubmit: true
	}, 'e.g. 12-3456789' ],

	'zip-code': [ {
		mask: '99999',
		removeMaskOnSubmit: true
	}, 'e.g. 12345' ],

	'ssn': [ {
		mask: '999-99-9999',
		removeMaskOnSubmit: true
	}, 'e.g. 123-45-6789' ],

	'currency': [ {
		alias: 'currency',
		rightAlign: false,
		removeMaskOnSubmit: true
	}, 'e.g. 100,000.00' ],

	'currency-integer': [ {
		alias: 'currency',
		rightAlign: false,
		removeMaskOnSubmit: true,
		digits: 0
	}, 'e.g. 100,000' ]
};

/**
 * Unmask all inputs inside a certain parent.
 *
 * @param {HTMLElement} parent
 */
export function unmaskInputs ( parent = document ) {
	Object.keys( masks ).forEach( selector => {
		const inputs = parent.querySelectorAll( `.js-mask-${selector}:not(:disabled):not(.read-only)` );

		inputs.forEach( input => {
			if ( input.inputmask ) {
				const { opts } = input.inputmask;

				if ( parent.contains( input ) ) {

					// Get the unmasked value.
					const originalValue = input.inputmask.unmaskedvalue();

					// Remove the mask instance.
					input.inputmask.remove();

					// Unmask the input if it has the "removeMaskOnSubmit" prop.
					if ( opts.removeMaskOnSubmit ) {
						input.value = originalValue;
					}
				}
			}
		});
	});
}

/**
 * Mask all inputs inside a certain parent.
 *
 * @param {HTMLElement} parent
 */
export function maskInputs ( parent = document ) {
	for ( const [ selector, [ config, placeholder ] ] of Object.entries( masks ) ) {
		const inputs = parent.querySelectorAll( `.js-mask-${selector}:not(.read-only)` );

		inputs.forEach( input => {
			input.placeholder = placeholder;

			// Init the mask.
			Inputmask({
				...config,
				clearIncomplete: true,
				clearMaskOnLostFocus: true
			}).mask( input );

			// Make it required to fill all optional characters.
			const { mask } = config;

			if ( mask && mask.includes( '[' ) ) {
				input.addEventListener( 'blur', () => {
					const cleanValue = input.value.replace( '_', '' );
					const requiredMask = mask.split( '[' )[0];
					const fullMask = mask.replace( '[', '' ).replace( ']', '' );

					if (
						cleanValue.length !== requiredMask.length &&
						cleanValue.length !== fullMask.length
					) {
						input.value = '';
					}
				});
			}
		});
	}
}
