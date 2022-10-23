import axios from 'axios';
import Swiper from '~/controllers/swiper';
import { maskInputs, unmaskInputs } from '~/helpers/mask-inputs';
import { setupComponents } from '~/domain/components';

export default class {
	constructor ( page ) {
		this.pageElement = page;
		this.pageTitle = page.querySelector( '.js-page-title h2' );
		this.steps = this.pageElement.querySelectorAll( '.js-step' );
		this.swiper = null;
		this.form = this.pageElement.querySelector( '.js-register' );
		this.memory = JSON.parse( localStorage.getItem( 'clear-portal-register' ) || null );
		this.csrfToken = this.form.querySelector( 'input[name="_token"]' );
	}

	updateMemory () {
		localStorage.setItem( 'clear-portal-register', JSON.stringify( this.memory ) );
	}

	setupInputsMemorization () {

		// If there are no memorized fields, start from an empty object.
		if ( ! this.memory ) this.memory = { step: 0 };

		// Move to last edited step.
		if ( this.memory.step > 0 ) {
			this.swiper.instance.slideTo( this.memory.step );
		}

		this.form.querySelectorAll( '[name]:not([type="file"]):not([name*="signature"])' ).forEach( input => {

			// Populate fields with previously added data.
			const existingValue = this.memory[ input.name ];
			if ( existingValue ) {
				input.value = existingValue;
				input.dispatchEvent( new Event( 'change' ) );
			}

			// Update the memory everytime any field is changed.
			input.addEventListener( 'change', () => {
				this.memory[ input.name ] = input.value;
				this.updateMemory();
			});
		});
	}

	setupSlider () {

		// Setup multi-step form with Swiper.
		this.swiperElement = this.pageElement.querySelector( '.js-swiper' );

		this.swiper = new Swiper({
			element: this.swiperElement,
			options: {
				grabCursor: false,
				autoHeight: true,
				spaceBetween: 10,
				allowTouchMove: false
			}
		});

		this.swiper.setup();

		// Setup observer to update Swiper when the contents change, so its height is updated.
		document.querySelectorAll( '.js-swiper-slide' ).forEach( slide => {
			new ResizeObserver( entries => {
				entries.forEach( () => {
					this.swiper.instance.update();
				});
			}).observe( slide );
		});

		// Setup each slide's buttons to control the slider.
		this.swiper.instance.slides.forEach( ( slide ) => {
			const buttonPrev = slide.querySelector( '.js-button-prev' );
			if ( buttonPrev ) buttonPrev.addEventListener( 'click', () => this.swiper.instance.slidePrev() );
		});

		// Update steps indicators while the form is filled.
		this.swiper.instance.on( 'slideChangeTransitionEnd', () => {
			const swiperIndex = this.swiper.instance.activeIndex;

			this.steps.forEach( step => {
				const stepIndex = Number( step.dataset.index );
				step.classList[ stepIndex <= swiperIndex ? 'add' : 'remove' ]( 'active' );
				step.classList[ stepIndex < swiperIndex ? 'add' : 'remove' ]( 'past' );
			});

			const { title } = this.swiper.instance.slides[ swiperIndex ].dataset;
			this.pageTitle.textContent = title;

			// Update the memory with the current step.
			this.memory.step = this.swiper.instance.activeIndex;
			this.updateMemory();
		});

		// Avoid HTML form validation of inputs in other form steps.
		this.swiper.instance.on( 'slideChange', () => this.updateVisibleInputs() );
	}

	updateVisibleInputs () {

		// Disable field groups of inactive slides.
		this.swiper.instance.slides.forEach( ( slide, slideIndex ) => {
			if ( slideIndex <= this.swiper.instance.activeIndex ) {
				slide.removeAttribute( 'disabled' );
			} else {
				slide.setAttribute( 'disabled', '' );
			}
		});
	}

	submitForm ( e ) {
		e.preventDefault();
		this.submitStep( this.swiper.instance.activeIndex );
	}

	async submitStep ( step ) {

		// Add class that disables form during submit.
		this.form.classList.add( 'submitting' );

		// Clean up form messages/errors.
		const messages = this.form.querySelector( '.js-form-messages' );
		if ( messages ) messages.remove();

		const inputErrors = this.form.querySelectorAll( '.input-error' );
		inputErrors.forEach( input => input.classList.remove( 'input-error' ) );

		// Check inputs validity before going to the next step.
		if ( this.form.checkValidity() ) {
			unmaskInputs( this.swiper.instance.slides[ this.swiper.instance.activeIndex ]);
			const data = new FormData( this.form );
			data.append( 'step', step );
			maskInputs( this.swiper.instance.slides[ this.swiper.instance.activeIndex ]);

			await axios.post( this.form.dataset.stepAction, data )
				.then( ({ data }) => {

					// Refresh CSRF token.
					this.csrfToken.value = data.token;

					if ( data.messages ) {

						// Prepare error messages.
						data.errors.forEach( error => {
							let inputName;

							if ( error.includes( '.' ) ) {
								const parts = error.split( '.' );
								inputName = `${parts[0]}[${parts[1]}][${parts[2]}]`;
							} else {
								inputName = error;
							}

							const input = this.form.querySelector( `*[name="${inputName}"]` );
							if ( input ) input.classList.add( 'input-error' );
						});

						// Append error messages.
						this.form.insertAdjacentHTML( 'afterBegin', data.messages );

						// Scroll to top of the page.
						this.form.scrollIntoView({
							behavior: 'smooth'
						});
					} else {
						if ( this.swiper.instance.isEnd ) {

							// And submit the form.
							unmaskInputs( this.form );
							this.form.removeEventListener( 'submit', this.submitForm );
							this.form.submit();
						} else {

							// Jump to the next step.
							this.swiper.instance.slideNext();

							// When needed, replace the next step fields.
							if ( data.inputs ) {
								const { slides, activeIndex } = this.swiper.instance;
								const nextSlide = slides[ activeIndex ];
								nextSlide.querySelector( '.js-step-fields' ).innerHTML = data.inputs;

								// Mask dynamically added inputs.
								maskInputs( nextSlide );

								// Init dynamically added components.
								setupComponents();
							}
						}
					}

					return;
				});
		} else {
			this.form.reportValidity();
		}

		// Remove class that disables the form during submit.
		this.form.classList.remove( 'submitting' );
	}

	setup () {
		this.setupSlider();
		this.setupInputsMemorization();
		this.updateVisibleInputs();

		// Avoid form native submit behavior.
		this.form.addEventListener( 'submit', this.submitForm.bind( this ) );
	}
}
