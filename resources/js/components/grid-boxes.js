import Swiper from '~/controllers/swiper';

export default class {
	constructor ( element ) {
		this.element = element;
	}

	setup () {
		this.swiperElement = this.element.querySelector( '.js-swiper' );

		// Return if the grid does not have a slider.
		if ( ! this.swiperElement ) return;

		const { slidesPerView } = this.swiperElement.dataset;

		const swiperConfig = {
			element: this.swiperElement,
			controls: this.element.querySelector( '.js-swiper-controls' )
		};

		if ( slidesPerView ) {
			swiperConfig.options = {
				breakpoints: {
					1200: {
						spaceBetween: 25,
						slidesPerView
					}
				}
			};
		}

		const swiper = new Swiper( swiperConfig );

		swiper.setup();
	}
}
