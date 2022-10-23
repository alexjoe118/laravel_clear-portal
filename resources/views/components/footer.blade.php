@php
	$privacyPolicyUrl = globalSettings('privacy_policy_url');
	$termsOfServiceUrl = globalSettings('terms_of_service_url');
@endphp

<footer class="footer js-footer">
	<span class="copyright">© {{ date('Y') }} Clear. {{ globalSettings('copyright_text') ?? '' }}</span>

	@if ($privacyPolicyUrl || $termsOfServiceUrl)
		<nav class="navigation">
			@if ($privacyPolicyUrl)
				<a href="{{ $privacyPolicyUrl }}" target="_blank">Privacy Policy</a>
			@endif

			@if ($termsOfServiceUrl)
				<a href="{{ $termsOfServiceUrl }}" target="_blank">Terms of Service</a>
			@endif
		</nav>
	@endif
</footer>
