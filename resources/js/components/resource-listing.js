import { debounce } from 'lodash';

export default class {
	constructor ( element ) {
		this.element = element;
		this.accordion = this.element.querySelectorAll( '.js-item' ).length ? this.element : null;
	}

	adjustItemHeight ( item ) {
		if ( ! item.classList.contains( 'active' ) ) return;

		const itemContent = item.querySelector( '.js-item-content' );
		if ( ! itemContent ) return;

		const itemContentWrapper = item.querySelector( '.js-item-content-wrapper' );
		const { clientHeight: height } = itemContentWrapper;
		itemContent.style.height = `${height}px`;

		itemContent.ontransitionend = ({ propertyName }) => {
			if ( propertyName === 'height' ) itemContent.style.overflow = 'visible';
		};
	}

	setup () {

		// Setup accordions.
		if ( this.accordion ) {

			// Listen for the 'resize' event on the window and adjust the items size.
			window.addEventListener( 'resize', debounce (
				() => this.accordion.querySelectorAll( '.js-item' ).forEach( item => this.adjustItemHeight( item ) ),
				200
			) );

			// Change the active item on click.
			this.accordion.addEventListener( 'click', ({ target }) => {
				const toggler = target.closest( '.js-toggler' );
				if ( ! toggler ) return;

				const item = target.closest( '.js-item' );

				// Close accordion.
				if ( item.classList.contains( 'active' ) ) {
					item.classList.remove( 'active' );

					const itemContent = item.querySelector( '.js-item-content' );
					itemContent.ontransitionend = null;
					itemContent.removeAttribute( 'style' );

				// Open accordion.
				} else {
					item.classList.add( 'active' );
					this.adjustItemHeight( item );
				}
			});
		}
	}
}
