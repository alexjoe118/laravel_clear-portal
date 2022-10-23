export default class {
	constructor ( page ) {
		this.pageElement = page;
		this.activeStats = null;
	}

	setup () {

		// Setup the stats list show/hide dynamics.
		this.pageElement.addEventListener( 'click', ({ target, clientX }) => {
			if ( ! target.closest( '.js-stats.active' ) && this.activeStats ) {
				this.activeStats.classList.remove( 'active' );
				this.activeStats = null;
			}

			if ( target.classList.contains( 'js-stats' ) ) {
				this.activeStats = target;
				this.activeStats.classList.remove( 'left', 'right' );
				this.activeStats.classList.add( 'active' );
				this.activeStats.classList.add( clientX < window.innerWidth / 2 ? 'left' : 'right' );
			}
		});
	}
}
