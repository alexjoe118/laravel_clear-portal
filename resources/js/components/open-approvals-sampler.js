import { camelCase } from 'lodash';

export default class {
	constructor ( element ) {
		this.element = element;
		this.loanProductGroups = this.element.querySelectorAll( '.js-loan-product-group' );
	}

	setupScrollIndicator () {
		const scrollIndicator = this.element.querySelector( '.js-scroll-indicator' );

		// Bail early if the scroll indicator is not needed.
		if ( ! scrollIndicator ) return;

		// Show scroll indicator if there are contents below.
		window.onscroll = () => {
			if ( window.scrollY > 100 ) {
				scrollIndicator.classList.remove( 'active' );
				window.onscroll = null;
			}
		};

		// Trigger scroll on page load.
		window.dispatchEvent( new Event( 'scroll' ) );
	}

	setupToggleBars () {
		this.loanProductGroups.forEach( loanProductGroup => {
			const toggleBar = loanProductGroup.querySelector( '.js-toggle-bar' );

			// Bail early f the Loan Product Group has no Toggle Bar.
			if ( ! toggleBar ) return;

			// The Loan Product ID of this group.
			const { loanProductId } = loanProductGroup.dataset;

			// The Open Approvals parts.
			const openApprovalsElements = loanProductGroup.querySelectorAll( '.js-open-approval' );
			const openApprovals = [ ...openApprovalsElements ].map( openApproval => {
				const parts = {};

				[
					'maximum-amount',
					'main-total-funding',
					'total-funding',
					'total-payback',
					'sample-draw',
					'term-length',
					'rate',
					'interest-rate',
					'multiplier',
					'payment-portion',
					'number-of-payments',
					'loan-amount'
				].forEach( part => {
					parts[ camelCase( part ) ] = openApproval.querySelector( `.js-${part}` );
				});

				return parts;
			});

			// The Toggle Bar parts.
			const input = toggleBar.querySelector( 'input' );
			const { min, max } = input;
			const [ track, thumb, status ] = [
				'track',
				'thumb',
				'status'
			].map( part => toggleBar.querySelector( `.js-${part}` ) );

			// Update range input.
			input.addEventListener( 'input', () => {
				const value = Number( input.value );

				// Update the range bar values.
				const progress = ( value - min ) / ( max - min ) * 100;
				track.style.width = progress === 0 ? 0 : `calc(${progress}% + ${thumb.clientWidth * ( 100 - progress ) / 100}px)`;
				thumb.style.left = `calc(${progress}% - ${thumb.clientWidth * progress / 100}px)`;
				status.textContent = value.toLocaleString();

				openApprovals.forEach( openApproval => {
					const {
						maximumAmount,
						mainTotalFunding,
						totalFunding,
						totalPayback,
						sampleDraw,
						termLength,
						rate,
						interestRate,
						multiplier,
						paymentPortion,
						numberOfPayments,
						loanAmount
					} = openApproval;

					// Limit value to not surpass maximum amount.
					let limitedValue = value;
					const maximumAmountValue = Number( maximumAmount.value );
					if ( value > maximumAmountValue ) limitedValue = maximumAmountValue;

					// Update each Open Approval within this Loan Product Group.
					if ([ '1', '2' ].includes( loanProductId ) ) {
						const rateValue = Number( rate.value );
						const numberOfPaymentsValue = Number( numberOfPayments.value );
						const paybackValue = limitedValue * ( 1 + rateValue / 100 );

						mainTotalFunding.textContent = this.formatValue( limitedValue );
						totalFunding.textContent = this.formatValue( limitedValue );
						loanAmount.value = limitedValue;
						totalPayback.textContent = this.formatValue( paybackValue );
						paymentPortion.textContent = this.formatValue( paybackValue / numberOfPaymentsValue );
					}

					if ([ '3', '4', '5', '6', '7' ].includes( loanProductId ) ) {
						const interestRateValue = Number( interestRate.value );
						const interestRatePerPeriod = interestRateValue / 100 / 12;
						const termLengthValue = Number( termLength.value );

						mainTotalFunding.textContent = this.formatValue( limitedValue );
						loanAmount.value = limitedValue;
						paymentPortion.textContent = this.formatValue(
							limitedValue * ( interestRatePerPeriod * Math.pow( ( 1 + interestRatePerPeriod ), termLengthValue ) ) /
							( Math.pow( ( 1 + interestRatePerPeriod ), termLengthValue ) - 1 )
						);
					}

					if ( loanProductId === '8' ) {
						const multiplierValue = Number( multiplier.value );
						const numberOfPaymentsValue = Number( numberOfPayments.value );
						sampleDraw.textContent = this.formatValue( limitedValue );
						paymentPortion.textContent = this.formatValue( limitedValue * multiplierValue / numberOfPaymentsValue );
					}
				});
			});

			const event = new Event( 'input' );

			// Adjust the Toggle Bar on page load.
			input.dispatchEvent( event );

			// Adjust the Toggle Bar on window resize.
			window.addEventListener( 'resize', () => input.dispatchEvent( event ) );
		});
	}

	formatValue ( value ) {
		return '$' + value.toLocaleString( 'en-US', { maximumFractionDigits: 0 });
	}

	setup () {
		this.setupToggleBars();
		this.setupScrollIndicator();
	}
}
