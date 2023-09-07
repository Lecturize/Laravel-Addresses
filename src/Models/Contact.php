<?php

namespace Lecturize\Addresses\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Contact
 * @package Lecturize\Addresses\Models
 *
 * @property-read int  $id
 *
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
 *
 * @property-read string  $full_name
 * @property-read string  $full_name_rev
 *
 * @property-read Model|null    $contactable
 * @property-read Address|null  $address
 */
class Contact extends Model
{
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
    protected $casts = [
        'properties' => 'array',

        'deleted_at' => 'datetime',
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

    private function updateFillables(): void
    {
        $fillable = $this->fillable;
        $columns  = preg_filter('/^/', 'is_', config('lecturize.addresses.columns', ['public', 'primary', 'billing', 'shipping']));

        $this->fillable(array_merge($fillable, $columns));
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public static function getValidationRules(): array
    {
        return config('lecturize.contacts.rules', []);
    }

    public function getFullNameAttribute(?bool $show_salutation = null): string
    {
        $show_salutation = (bool) $show_salutation;

        $names = [];
        $names[] = $show_salutation && $this->gender ? trans('addresses::contacts.salutation.'. $this->gender) : '';
        $names[] = $this->first_name  ?: '';
        $names[] = $this->middle_name ?: '';
        $names[] = $this->last_name   ?: '';

        return trim(implode(' ', array_filter($names)));
    }

    public function getFullNameRevAttribute(?bool $show_salutation = null): string
    {
        $first = [];
        $first[] = $this->first_name  ?: '';
        $first[] = $this->middle_name ?: '';

        $show_salutation = (bool) $show_salutation;

        $last = [];
        $last[] = $show_salutation && $this->gender ? trans('addresses::contacts.salutation.'. $this->gender) : '';
        $last[] = $this->last_name ?: '';

        $names = [];
        $names[] = implode(' ', array_filter($last));
        $names[] = implode(' ', array_filter($first));

        return trim(implode(', ', array_filter($names)));
    }

    public function scopeFlag(Builder $query, string $flag): Builder
    {
        return $query->where('is_'.$flag, true);
    }
}
