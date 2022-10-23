<div class="input-multiline">
	<textarea
		onkeyup="event.preventDefault()"
		{{ renderAttrs($input) }}>{{ $defaultValue ?? '' }}</textarea>
</div>
