<?php

namespace Kwidoo\Contacts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;


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

        'vat_id',

        'notes',
        'properties',

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

        $this->table = config('contacts.tables.main', 'contacts');
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
                $model->uuid = Uuid::generate()->string;
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
        $columns  = [config('contacts.tax_column', 'vat_id')];

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
    public function values(): HasMany
    {
        return $this->hasMany(Address::class);
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
