@php
    $accordion = $accordion ?? false;
    $resourceType = Str::between(Route::currentRouteName(), '.', '.');
@endphp

<div class="resource-listing js-resource-listing">
    {{ $slot }}

    <div class="my_pagination pagination">
        @if ($curpage-1 > 0)
			@include('components.button', [
				'icon' => 'svg.arrow-right',
				// 'url' => $pagination->previousPageUrl(),
				'class' => 'arrow-prev',
				'onclick' =>"getResult('$resource', 'prev')",
			])
        @endif
        @if ($curpage < $pages )
			@include('components.button', [
				'icon' => 'svg.arrow-right',
				// 'url' => $pagination->previousPageUrl(),
				'class' => 'arrow-next',
				'onclick' =>"getResult('$resource', 'next')",
			])
        @endif
       
		@if($pages > 1)
			<span class="status">Page {{$curpage}} of {{$pages}}</span>	
		@endif
		
        <input type="hidden" name="rowcount" id="rowcount" />
        <input type="hidden" id="{{ $resource }}-curpage" value={{$curpage}} />
        <input type="hidden" id="{{ $resource }}-pages" value={{$pages}} /> 
    </div>
</div>
	
