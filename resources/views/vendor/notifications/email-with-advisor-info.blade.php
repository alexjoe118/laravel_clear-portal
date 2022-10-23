@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
# @lang('Hello!')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines ?? [] as $line)
{{ $line }}

@endforeach

{{-- Top Action Button --}}
@isset($topActionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $topActionUrl])
{{ $topActionText }}
@endcomponent
@endisset

{{-- Outro Line --}}
@if (is_array($outroLines))
	@foreach ($outroLines ?? [] as $line)
	{{ $line }}

	@endforeach
@else
{{ $outroLines }}
@endif

{{-- List Items --}}
<ul>
@foreach ($listItems ?? [] as $item)
<li>{{ $item }}</li>
@endforeach
</ul>

{{-- Loan Data --}}
@foreach ($loanData ?? [] as $value)
{{ $value }}

@endforeach

{{-- Advisor Info --}}
@if (! empty($advisor))

@component('mail::panel')
<b>Loan Advisor</b>

<div class="avatar">
<img src="{{ $advisor['picture'] }}" />
{{ $advisor['full_name'] }}
</div>

{{ $advisor['email'] }}

{{ $advisor['phone_number'] }}

@if ($advisor['cell_phone_number'])
Cell: {{ $advisor['cell_phone_number'] }}

@endif

@endcomponent

@endif

{{-- Bottom Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Requested Text --}}
@isset($requestedText)
{{ $requestedText }}
@endisset

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
@endslot
@endisset
@endcomponent

<style>
/**
 * Media queries must be in the blade file, not work on theme.css
 */
@media screen and (max-width: 600px) {
	.panel {
		max-width: 100% !important;
	}
}
</style>
