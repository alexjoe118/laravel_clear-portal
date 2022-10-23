<?php

use App\Models\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Container\Container;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

if (! function_exists('renderAttrs')) {
	/**
	 * Render input attributes.
	 *
	 * @param array $attrs
	 * @return string
	 */
	function renderAttrs($attrs)
	{
		$attrsArray = [];

		foreach ($attrs as $attr => $value) {
			if (is_bool($value)) {
				if ($value) echo $attr . ' ';
			} else {
				printf('%s="%s"', $attr, $value);
			}
		}

		return join(' ', $attrsArray);
	}
}

if (! function_exists('prepareStyles')) {
	/**
	 * Prepare the style classes for components.
	 *
	 * @param mixed $styles
	 * @return string
	 */
	function prepareStyles($styles)
	{
		if (! $styles) return '';

		$styles = $styles ?? [];
		$styles = is_array($styles) ? $styles : explode(' ', $styles);
		$styles = array_map(function($value) {
			return 'style-' . $value;
		}, $styles);
		$styles = join(' ', $styles);

		return $styles;
	}
}

if (! function_exists('formatPhoneNumber')) {
	/**
	 * Format the US phone number.
	 *
	 * @param string $phone
	 * @return string
	 */
    function formatPhoneNumber($phone)
	{
		$prefix = Str::substr($phone, 0, 3);
		$first3 = Str::substr($phone, 3, 3);
		$last4 = Str::substr($phone, 6, 4);
		$ext = Str::substr($phone, 10, 3);
		if ($ext) $ext = " ext $ext";

		return "($prefix) $first3-$last4$ext";
    }
}

if (! function_exists('formatSsn')) {
	/**
	 * Format the SSN.
	 *
	 * @param string $phone
	 * @return string
	 */
    function formatSsn($ssn)
	{
		$first3 = Str::substr($ssn, 0, 3);
		$middle2 = Str::substr($ssn, 3, 2);
		$last4 = Str::substr($ssn, 5, 4);

		return "$first3-$middle2-$last4";
    }
}

if (! function_exists('globalSettings')) {
	/**
	 * Return a global settings value.
	 *
	 * @param string $phone
	 * @return string|array|null
	 */
    function globalSettings($key)
	{
		$row = Settings::firstWhere('key', $key);

		return $row ? $row->value : null;
    }
}

if (! function_exists('formatBytes')) {
	/**
	 * Create human-readable file size.
	 *
	 * @param string $bytes
	 * @return string
	 */
	function formatBytes($bytes)
	{
		if ($bytes === 0) return '0 bytes';

		$units = ['bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		$i = floor(log($bytes) / log(1024));

		return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
	}
}

if (! function_exists('getTitle')) {
	/**
	 * Return the best title for a resource.
	 *
	 * @param mixed $resource
	 * @return string
	 */
	function getTitle($resource)
	{
		return $resource->listing_title
			?? $resource->full_name
			?? $resource->name
			?? $resource->title
			?? '';
	}
}

if (! function_exists('prepareOptionsArray')) {
	/**
	 * Prepare the options to be an assoc array.
	 *
	 * @param object|array $options
	 * @return array
	 */
	function prepareOptionsArray($options)
	{
		if (Arr::isAssoc(collect($options)->toArray())) return $options;

		return collect($options)->mapWithKeys(function($option) {
			return [(string) Str::of($option)->lower()->replace(' ', '-') => $option];
		});
	}
}

if (! function_exists('paginateCollection')) {
	/**
     * Create a new length-aware paginator instance.
     *
     * @param \Illuminate\Support\Collection $items
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
	function paginateCollection(Collection $results, $perPage)
	{
		$page = Paginator::resolveCurrentPage('page');

		return Container::getInstance()->makeWith(LengthAwarePaginator::class, [
			'items' => $results->forPage($page, $perPage),
			'total' => $results->count(),
			'perPage' => $perPage,
			'currentPage' => $page,
			'options' => [
				'path' => Paginator::resolveCurrentPath(),
				'pageName' => 'page'
			]
		]);
    }
}
