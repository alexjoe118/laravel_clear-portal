export default class {
	constructor ( dashboard ) {
		this.dashboard = dashboard;
		this.sidebar = this.dashboard.querySelector( '.js-sidebar' );
		this.toggler = this.dashboard.querySelector( '.js-sidebar-toggler' );
		this.mobileActive = false;
		this.desktopActive = true;
	}

	toggleSidebar () {
		const mobile = window.innerWidth < 1024;

		if ( mobile ) {
			this.sidebar.classList[ this.mobileActive ? 'remove' : 'add' ]( 'mobile-active' );
			this.mobileActive = ! this.mobileActive;
		} else {
			this.sidebar.classList[ this.desktopActive ? 'remove' : 'add' ]( 'desktop-active' );
			this.desktopActive = ! this.desktopActive;
		}
	}

	setup () {

		// Open sidebar when clicking the toggler.
		this.toggler.addEventListener( 'click', () => this.toggleSidebar() );
	}
}
