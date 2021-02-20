<?php namespace Lecturize\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Lecturize\Addresses\Traits\HasCountry;

/**
 * Class Contact
 * @package Lecturize\Addresses\Models
 */
class Contact extends Model
{
    use HasCountry;
    use SoftDeletes;

    /** @inheritdoc */
    protected $fillable = [
        'gender',
        'title',
        'first_name',
        'middle_name',
        'last_name',

        'company',
        'extra',
        'position',

        'phone',
        'mobile',
        'fax',
        'email',
        'website',

        'notes',
        'properties',

        'address_id',

        'contactable_id',
        'contactable_type',
    ];

    /** @inheritdoc */
    protected $dates = [
        'deleted_at',
    ];

    /** @inheritdoc */
    protected $casts = [
        'properties' => 'array',
    ];

    /** @inheritdoc */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('lecturize.contacts.table', 'contacts');
        $this->updateFillables();
    }

    /** @inheritdoc */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->getConnection()
                      ->getSchemaBuilder()
                      ->hasColumn($model->getTable(), 'uuid'))
                $model->uuid = \Webpatser\Uuid\Uuid::generate()->string;
        });
    }

    /**
     * Update fillable fields dynamically.
     *
     * @return void.
     */
    private function updateFillables()
    {
        $fillable = $this->fillable;
        $columns  = preg_filter('/^/', 'is_', config('lecturize.addresses.columns', ['public', 'primary', 'billing', 'shipping']));

        $this->fillable(array_merge($fillable, $columns));
    }

    /**
     * Get the related model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function contactable()
    {
        return $this->morphTo();
    }

    /**
     * Get the address that might own this contact.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function getValidationRules()
    {
        $rules = [];

        return $rules;
    }

    /**
     * Get the contacts full name.
     *
     * @param  bool $show_salutation
     * @return string
     */
    public function getFullNameAttribute($show_salutation = false)
    {
        $names = [];
        $names[] = $show_salutation && $this->gender ? trans('addresses::contacts.salutation.'. $this->gender) : '';
        $names[] = $this->first_name  ?: '';
        $names[] = $this->middle_name ?: '';
        $names[] = $this->last_name   ?: '';

        return trim(implode(' ', array_filter($names)));
    }

    /**
     * Get the contacts full name, reversed.
     *
     * @param  bool $show_salutation
     * @return string
     */
    public function getFullNameRevAttribute($show_salutation = false)
    {
        $first = [];
        $first[] = $this->first_name  ?: '';
        $first[] = $this->middle_name ?: '';

        $last = [];
        $last[] = $show_salutation && $this->gender ? trans('addresses::contacts.salutation.'. $this->gender) : '';
        $last[] = $this->last_name ?: '';

        $names = [];
        $names[] = implode(' ', array_filter($last));
        $names[] = implode(' ', array_filter($first));

        return trim(implode(', ', array_filter($names)));
    }
}