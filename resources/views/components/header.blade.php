@php
	$structure = $structure ?? '';
@endphp

<header class="header js-header">
	@if($structure === 'dashboard')
		<button class="sidebar-toggler js-sidebar-toggler">
			<div aria-hidden="true"></div>
			<div aria-hidden="true"></div>
		</button>
	@else
		@include('components.picture', [
			'src' => url('images/white-logo.svg'),
			'class' => 'logo',
			'link' => [
				'url' => route('login'),
				'title' => 'Return to Homepage'
			]
		])
	@endif

	<nav class="navigation">
		@if (! Auth::check())
			<div class="links">
				@if (globalSettings('contact_email'))
					<a href="mailto:{{ globalSettings('contact_email') }}">Talk To Us</a>
				@endif
			</div>

			@if (! Route::is('login') && ! Route::is('verification.notice'))
				@include('components.button', [
					'url' => url('login'),
					'title' => 'Login'
				])
			@endif

			@if (! Route::is('register') && ! Route::is('verification.notice') && ! Route::is('guest.open-approvals'))
				@include('components.button', [
					'url' => url('register'),
					'title' => 'Sign Up'
				])
			@endif
		@elseif ($structure === 'dashboard' && Auth::user()->isNotAdmin())
			@if (globalSettings('contact_email'))
				@include('components.button', [
					'url' => 'mailto:' . globalSettings('contact_email'),
					'title' => 'Contact',
					'icon' => false,
				])
			@endif

			@include('components.button', [
				'tag' => 'button',
				'type' => 'button',
				'icon' => 'svg.bell',
				'attrs' => [
					'data-notifications' => $userNotificationsNew
				],
				'class' => 'notifications-toggler '. ( $userNotificationsNew > 0 ? 'has-notifications ' : '' ) .'js-notifications-toggler'
			])

			<div class="notifications js-notifications">
				@if(count($userNotifications) > 0)
					<div class="wrapper">
						@foreach($userNotifications as $notification)
							<div
								class="notification {{ ! $notification->read ? 'new' : '' }} js-notification"
								data-action="{{ route('user.notification.update', ['id' => $notification->id]) }}"
								data-read="{{ $notification->read }}">
								@csrf

								<button type="button" class="status js-notification-status"></button>

								<div class="info">
									@php
										$titleTag = $notification->url ? 'a' : 'span';
									@endphp

									<{{ $titleTag }}
										class="title {{ $notification->url ? 'js-notification-url' : '' }}"
										@if ($notification->url)
											href="{{ $notification->url }}"
										@endif>

										{{ $notification->title }}

										@if ($notification->url)
											@include('svg.arrow-right')
										@endif
									</{{ $titleTag }}>

									<span class="message text-small">{!! nl2br($notification->message) !!}</span>

									<span class="date text-extra-small">{{ $notification->created_at->diffForHumans() }}</span>
								</div>
							</div>
						@endforeach
					</div>
				@else
					<span class="empty-message text-small">You don't have any notifications.</span>
				@endif
			</div>
		@endif

		@if (Auth::check())
			<form
				method="POST"
				action="{{ route('logout') }}"
				class="logout">
				@csrf

				<button type="submit" class="text-normal">Logout</button>
			</form>
		@endif
	</nav>
</header>
