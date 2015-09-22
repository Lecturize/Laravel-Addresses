<?php namespace vendocrat\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Countries\Countries;

/**
 * Class Address
 * @package vendocrat\Addresses\Models
 */
class Address extends Model
{
	use SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'addresses';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'street',
		'city',
		'state',
		'post_code',
		'country_id',
		'lat',
		'lng',
		'addressable_id',
		'addressable_type',
		'is_primary',
		'is_billing',
		'is_shipping',
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * {@inheritdoc}
	 */
	public function country()
	{
		return $this->belongsTo(Countries::class, 'country_id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function addressable()
	{
		return $this->morphTo();
	}

	/**
	 * {@inheritdoc}
	 */
	public static function boot() {
		parent::boot();

		static::saving(function($address) {
			if(\Config::get('addresses.geocode')) {
				$address->geocode();
			}
		});
	}

	/**
	 * @return array
	 */
	public static function getValidationRules() {
		$rules = [
			'street'     => 'required|string|min:3|max:60',
			'city'       => 'required|string|min:3|max:60',
			'state'      => 'string|min:3|max:60',
			'post_code'  => 'required|min:4|max:10|AlphaDash',
			'country_id' => 'required|integer',
		];

		foreach( \Config::get('addresses.flags') as $flag ) {
			$rules['is_'.$flag] = 'boolean';
		}

		return $rules;
	}

	/**
	 * Using the address in memory, fetch get latitude and longitude
	 * from google maps api and set them as attributes
	 */
	public function geocode() {
		// build query string
		$query = [];
		$query[] = $this->street        ?: '';
		$query[] = $this->city          ?: '';
		$query[] = $this->state         ?: '';
		$query[] = $this->post_code     ?: '';

		if ( $this->country && $country = $this->country->name )
			$query[] = $country;

		$query = trim( implode(',', array_filter($query)) );
		$query = str_replace(' ', '+', $query);

		$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$query.'&sensor=false');
		$output  = json_decode($geocode);

		if ( count($output->results) ) {
			$this->lat = $output->results[0]->geometry->location->lat;
			$this->lng = $output->results[0]->geometry->location->lng;
		} else {
		//	throw new InvalidValueException('Address Could Not be Validated');
		}

		return $this;
	}

	/**
	 * Get formatted address
	 */
	public function getAddress() {
		$str = [];

		foreach ( array('street', 'street_extra') as $line ) {
			if ( strlen($this->{$line} ) ) {
				$str []= $this->{$line};
			}
		}

		if ( strlen($this->city) ) {
			$str []= sprintf('%s, %s %s', $this->city, $this->state, $this->zip);
		}

		return implode(', ', $str);
	}
}