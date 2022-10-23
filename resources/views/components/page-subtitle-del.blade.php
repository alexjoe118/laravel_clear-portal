<span class="page-subtitle {{ $class ?? '' }} {{ isset($slot) && $slot->isNotEmpty() ? 'with-slot' : '' }} " id="focus-{{$resource}}">
	@if($subtitle)
		<span>{{ $subtitle }}</span>
	@endif
	<div class="line" aria-hidden="true"></div>
	@include('components.form-fields', [
					'fields' => [
						[
							'width' => 'first',
							'type' => 'select',
							'options' => $selectOptions['pageCount'],
							'defaultValue' => $defaultValue,
							'input' => [
								'onchange' => 'getResult(\''.$resource.'\', \'option\')',
								'onlyitem' => true,
								'id' => $resource.'-pageoption',
							],
							
						],
					],
				])
	
	{{ $slot ?? '' }}
</span>
