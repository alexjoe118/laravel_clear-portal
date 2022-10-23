<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

	/**
	 * Avoid form duplicated submissions.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return void
	 */
	protected function tokensMatch($request)
	{
		$token = $this->getTokenFromRequest($request);

		$tokensMatch = $request->session()->token() === $token;

		if ($tokensMatch) $request->session()->regenerateToken();

		return $tokensMatch;
	}
}
