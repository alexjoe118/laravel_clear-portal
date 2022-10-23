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

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'primary';
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@endisset

@component('mail::button', ['url' => $actionUrl, 'color' => $color ?? 'primary'])
{{ $actionText }}
@endcomponent

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
<li style="margin-bottom: 10px">{{ $item }}</li>
@endforeach
</ul>

{{-- Loan Data --}}
@foreach ($loanData ?? [] as $value)
{{ $value }}

@endforeach

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
