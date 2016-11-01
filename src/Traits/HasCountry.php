<?php namespace Lecturize\Addresses\Traits;

use Webpatser\Countries\Countries;

/**
 * Class HasCountry
 * @package Lecturize\Addresses\Traits;
 */
trait HasCountry
{
	/**
	 * Get the models country.
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function country()
	{
		return $this->hasOne(Countries::class);
	}

	/**
	 * Scope by country.
	 *
	 * @param $query
	 * @param string $locale
	 * @return mixed
	 */
	public function scopeLanguages( $query, $locale = '' )
	{
		if ( ! $locale )
			$locale = app()->getLocale();

		return $query->whereHas('language', function($q) use($locale) {
			$q->where( 'locale', $locale );
		});
	}
}