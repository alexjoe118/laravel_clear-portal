<aside class="sidebar js-sidebar desktop-active">
	<div class="logo">
		@include('components.picture', [
			'src' => url('images/white-logo.svg'),
			'link' => [
				'url' => route('dashboard.index'),
				'title' => 'Return to Dashboard Index'
			]
		])
	</div>

	<a
		class="user"
		href="{{ route('user.edit') }}">

		@include('components.picture', [
			'src' => Auth::user()->photo,
			'class' => 'photo'
		])

		<div class="info text-small">
			<span class="name">{{
				Auth::user()->isAdmin()
				? Auth::user()->full_name
				: Auth::user()->business->name
			}}</span>

			<span class="id">{{
				Auth::user()->isNotAdmin()
				? 'Account #' . Auth::user()->customer_id
				: Str::title(Auth::user()->role)
			}}</span>
		</div>
	</a>

	<nav class="menu">
		@foreach($sidebarMenu as $item)
			@if (in_array(Auth::user()->role, $item['role']))
				<a
					href="{{ route($item['url']) }}"
					class="menu-item {{ Route::is(Str::beforeLast($item['url'], '.') . '.*') ? 'active' : '' }}">

					<span class="icon">@include('svg.' . $item['icon'])</span>
					<span class="label">{{ $item['title'] }}</span>
				</a>
			@endif
		@endforeach
	</nav>

	@if ($userAdvisor)
		<div class="advisor">
			<div class="info">
				@include('components.picture', [
					'src' => $userAdvisor->photo,
					'class' => 'photo'
				])

				<span class="name text-small">{{ $userAdvisor->full_name }}</span>
			</div>

			<span class="role">Loan Advisor</span>

			<div class="contact text-small">
				<a
					class="email"
					href="mailto:{{ $userAdvisor->email }}">
					{{ $userAdvisor->email }}
				</a>

				@if($userAdvisor->phone_number)
					<a
						class="phone-number"
						href="tel:{{ $userAdvisor->phone_number }}">
						{{ formatPhoneNumber($userAdvisor->phone_number) }}
					</a>
				@endif

				@if($userAdvisor->cell_phone_number)
					<a
						class="phone-number"
						href="tel:{{ $userAdvisor->cell_phone_number }}">
						Cell: {{ formatPhoneNumber($userAdvisor->cell_phone_number) }}
					</a>
				@endif

				<div>
					@include('components.button', [
						'title' => 'Contact',
						'url' => 'mailto:' . $userAdvisor->email
					])
				</div>
			</div>
		</div>
	@endif
</aside>
