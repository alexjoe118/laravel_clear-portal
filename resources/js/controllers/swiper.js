import Swiper, { Navigation } from 'swiper';

export default class {
	constructor ({
		element,
		controls = false,
		indicator = false,
		mobileOnly = false,
		mobileOnlyBreakpoint = 1280,
		options = {}
	}) {
		this.element = element;
		this.controls = controls;
		this.indicator = indicator;
		this.mobileOnly = mobileOnly;
		this.mobileOnlyBreakpoint = mobileOnlyBreakpoint;
		this.instance = null;
		this.options = options;
		this.breakpoint = ! this.mobileOnly ? null : window.matchMedia( `(max-width: ${this.mobileOnlyBreakpoint}px)` );
		this.mirrorSliders = [];
	}

	/**
	 * Sets the current slider to automatically control a list of other sliders.
	 *
	 * This is the method used when instantiating the slider.
	 *
	 * @param {object|object[]} otherSliders
	 */
	mirror ( otherSliders ) {
		if ( ! Array.isArray( otherSliders ) ) {
			otherSliders = [ otherSliders ];
		}

		for ( const otherSlider of otherSliders ) {
			this.mirrorSliders.push( otherSlider );
		}
	}

	/**
	 * Creates the Swiper object for the slider.
	 *
	 * @internal
	 */
	createSlider () {
		this.instance = new Swiper( this.element, this.options );

		if ( ! this.instance ) return;

		// Start controlling other sliders.
		this.instance.on( 'activeIndexChange', () => {
			const { realIndex: currentIndex } = this.instance;

			for ( const otherSlider of this.mirrorSliders ) {
				if ( otherSlider.getSlider().realIndex !== currentIndex ) {
					const a = otherSlider.getOption( 'loop' ) ? 'slideToLoop' : 'slideTo';
					otherSlider.getSlider()[ a ]( currentIndex );
				}
			}
		});

		if ( ! this.controls ) return;

		// Show the slider controls.
		this.controls.classList.add( 'active' );
	}

	/**
	 * Initializes the slider functionality.
	 *
	 * @internal
	 */
	setup () {
		if ( ! this.element || ! ( this.element instanceof HTMLElement ) ) {
			throw new Error( 'Slider element not specified.' );
		}

		if ( ! ( this.controls instanceof HTMLElement ) ) {
			this.controls = this.element.querySelector( '.js-swiper-controls' );
		}

		if ( ! ( this.indicator instanceof HTMLElement ) ) {
			this.indicator = false;
		}

		// Default Swiper options.
		const defaultOptions = {
			grabCursor: this.options.grabCursor ? this.options.grabCursor : true,
			slidesPerView: 'auto',
			modules: [ Navigation ],
			navigation: {
				prevEl: this.controls ? this.controls.querySelector( '.js-swiper-prev' ) : null,
				nextEl: this.controls ? this.controls.querySelector( '.js-swiper-next' ) : null
			}
		};

		this.options = { ...defaultOptions, ...this.options };

		// Add a breakpoint checker listener, if the slider is mobile-only.
		if ( this.mobileOnly ) {

			/**
			 * Checks the current state of the slider, if it's mobile-only.
			 *
			 * - If viewport width is higher than the breakpoint, destroys the slider.
			 * - If viewport width is lower than the breakpoint, recreates the slider.
			 *
			 * @internal
			 */
			const checkBreakpoint = () => {
				const isMobile = this.breakpoint.matches;

				if ( isMobile && ! this.instance ) return this.createSlider();

				if ( ! isMobile && this.instance ) {
					this.instance.destroy();
					this.instance = null;
				}
			};

			/**
			 * This is required because `addEventListener` is not
			 * supported in Safari yet.
			 */
			if ( typeof this.breakpoint.addEventListener !== 'undefined' ) {
				this.breakpoint.addEventListener( 'change', checkBreakpoint );
			} else {
				this.breakpoint.addListener( checkBreakpoint );
			}
		}

		// Create the slider.
		if (
			! this.mobileOnly ||
			( this.mobileOnly && this.breakpoint.matches )
		) this.createSlider();
	}
}
