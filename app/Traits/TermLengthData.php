<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait TermLengthData
{
	/**
	 * Display the term length with the unit.
	 */
	public function getTermLengthFormattedAttribute()
	{
		// Display in years.
		if ($this->term_length_display === 'years') {
			$years = intdiv($this->term_length, 12);
			$years .= ' Year' . ($years > 1 ? 's' : '');

			$months = $this->term_length % 12;
			if ($months > 0) $years .= " $months Month" . ($months > 1 ? 's' : '');

			return $years;
		}

		// Display in months.
		return "{$this->term_length} Month" . ($this->term_length > 1 ? 's' : '');
	}
}
