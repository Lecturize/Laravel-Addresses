<?php namespace vendocrat\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
		'name',
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
	 * Boot function
	 */
	public static function boot() {
		parent::boot();

		static::saving(function($address) {
			if(\Config::get('addresses::geocode')) {
				$address->geocode();
			}
		});
	}

	/**
	 * @return array
	 */
	public static function rules() {
		$rules = array(
			'adressee' =>'Max:100',
			'street'   =>'required|max:100',
			'city'     =>'required',
			'zip'      =>'required|min:4|max:10|AlphaDash',
		);

		return $rules;
	}

	/**
	 * Using the address in memory, fetch get latitude and longitude
	 * from google maps api and set them as attributes
	 */
	public function geocode() {
		$str = [];

		if( ! empty($this->zip) ) {
			$str[] = $this->street;
			$str[] = sprintf('%s, %s %s', $this->city, $this->state, $this->zip);
			$str[] = $this->country_name;
		}

		$query = str_replace(' ', '+', implode(', ', $str));

		$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$query.'&sensor=false');
		$output  = json_decode($geocode);

		if ( count($output->results) ) {
			$this->latitude  = $output->results[0]->geometry->location->lat;
			$this->longitude = $output->results[0]->geometry->location->lng;
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