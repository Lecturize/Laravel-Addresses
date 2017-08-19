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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Countries::class);
    }

    /**
     * Scope by country.
     *
     * @param  object   $query
     * @param  integer  $id
     * @return mixed
     */
    public function scopeByCountry( $query, $id )
    {
        return $query->whereHas('country', function($q) use($id) {
            $q->where('id', $id);
        });
    }
}