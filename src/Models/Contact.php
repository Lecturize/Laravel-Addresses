<?php

namespace Kwidoo\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use Kwidoo\Contacts\Traits\HasCountry;

/**
 * Class Contact
 * @package Kwidoo\Contacts\Models
 * @property string|null   $gender
 * @property string|null   $title
 * @property string|null   $first_name
 * @property string|null   $middle_name
 * @property string|null   $last_name
 * @property string|null   $company
 * @property string|null   $extra
 * @property string|null   $position
 * @property string|null   $phone
 * @property string|null   $mobile
 * @property string|null   $fax
 * @property string|null   $email
 * @property string|null   $email_invoice
 * @property string|null   $website
 * @property string|null   $vat_id
 * @property string|null   $notes
 * @property array|null    $properties
 * @property int|null      $address_id
 * @property Address|null  $address
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
        'email_invoice',
        'website',

        'vat_id',

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

        $this->table = config('contacts.table', 'contacts');
        $this->updateFillables();
    }

    /** @inheritdoc */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->getConnection()
                ->getSchemaBuilder()
                ->hasColumn($model->getTable(), 'uuid')
            )
                $model->uuid = \Webpatser\Uuid\Uuid::generate()->string;
        });
    }

    /**
     * Update fillable fields dynamically.
     *
     * @return void
     */
    private function updateFillables(): void
    {
        $fillable = $this->fillable;
        $columns  = preg_filter('/^/', 'is_', config('addresses.columns', ['public', 'primary', 'billing', 'shipping']));

        $this->fillable(array_merge($fillable, $columns));
    }

    /**
     * Get the related model.
     *
     * @return MorphTo
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the address that might own this contact.
     *
     * @return BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    public static function getValidationRules(): array
    {
        return config('contacts.rules', []);
    }

    /**
     * Get the contacts full name.
     *
     * @param  bool  $show_salutation
     * @return string
     */
    public function getFullNameAttribute(bool $show_salutation = false): string
    {
        $names = [];
        $names[] = $show_salutation && $this->gender ? trans('addresses::contacts.salutation.' . $this->gender) : '';
        $names[] = $this->first_name  ?: '';
        $names[] = $this->middle_name ?: '';
        $names[] = $this->last_name   ?: '';

        return trim(implode(' ', array_filter($names)));
    }

    /**
     * Get the contacts full name, reversed.
     *
     * @param  bool  $show_salutation
     * @return string
     */
    public function getFullNameRevAttribute(bool $show_salutation = false): string
    {
        $first = [];
        $first[] = $this->first_name  ?: '';
        $first[] = $this->middle_name ?: '';

        $last = [];
        $last[] = $show_salutation && $this->gender ? trans('addresses::contacts.salutation.' . $this->gender) : '';
        $last[] = $this->last_name ?: '';

        $names = [];
        $names[] = implode(' ', array_filter($last));
        $names[] = implode(' ', array_filter($first));

        return trim(implode(', ', array_filter($names)));
    }
}
