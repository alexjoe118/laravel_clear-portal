import axios from 'axios';

export default class {
	constructor ( dashboard ) {
		this.dashboard = dashboard;
		this.notifications = this.dashboard.querySelector( '.js-notifications' );
	}

	async updateNotification ( notification ) {
		const { action, read } = notification.dataset;
		const token = notification.querySelector( 'input[name="_token"]' ).value;

		await axios.put( action, { read, token })
			.then( ({ data }) => {

				// Update the notification status/style.
				notification.dataset.read = data.read;
				notification.classList[ data.read ? 'remove' : 'add' ]( 'new' );

				// Update the notifications toggler counter.
				this.total = Number( this.total ) + ( data.read ? -1 : 1 );
				this.notificationsToggler.dataset.notifications = this.total;
				this.notificationsToggler.classList[ this.total > 0 ? 'add' : 'remove' ]( 'has-notifications' );
			});
	}

	setup () {

		// Return if there are no notifications on the page.
		if ( ! this.notifications ) return;

		this.notificationsToggler = this.dashboard.querySelector( '.js-notifications-toggler' );
		this.total = this.notificationsToggler.dataset.notifications;

		// Show/hide the notifications box.
		document.addEventListener( 'click', ({ target }) => {
			if ( target.closest( '.js-notifications' ) ) return;

			const clickedToggler = target.classList.contains( 'js-notifications-toggler' );

			this.notifications.classList[ clickedToggler ? 'add' : 'remove' ]( 'active' );
		});

		// Read/unread notifications.
		this.dashboard.querySelectorAll( '.js-notification-status' ).forEach( toggler => {
			const notification = toggler.closest( '.js-notification' );
			toggler.addEventListener( 'click', () => this.updateNotification( notification ) );
		});

		this.dashboard.querySelectorAll( '.js-notification-url' ).forEach( url => {
			const notification = url.closest( '.js-notification' );

			url.addEventListener( 'click', async e => {
				e.preventDefault();

				if ( notification.dataset.read == 0 ) {
					await this.updateNotification( notification );
				}

				window.location.href = url.href;
			});
		});
	}
}
