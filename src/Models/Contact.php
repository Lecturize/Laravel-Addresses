<?php

namespace Lecturize\Addresses\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lecturize\Addresses\Factories\AddressFactory;
use Lecturize\Addresses\Factories\ContactFactory;
use Lecturize\Addresses\Helpers\NameGenerator;

/**
 * Class Contact
 * @package Lecturize\Addresses\Models
 *
 * @property-read int          $id
 * @property-read string|null  $uuid
 *
 * @property string|null  $gender
 * @property string|null  $title_before
 * @property string|null  $title_after
 * @property string|null  $first_name
 * @property string|null  $middle_name
 * @property string|null  $last_name
 * @property string|null  $company
 * @property string|null  $extra
 *
 * @property string|null  $position
 * @property string|null  $phone
 * @property string|null  $mobile
 * @property string|null  $fax
 * @property string|null  $email
 * @property string|null  $email_invoice
 * @property string|null  $website
 * @property string|null  $vat_id
 * @property string|null  $notes
 * @property array|null   $properties
 * @property int|null     $address_id
 *
 * @property-read string  $full_name
 * @property-read string  $full_name_rev
 *
 * @property-read Model|null    $contactable
 * @property-read Address|null  $address
 *
 * @method static Builder|Contact flag(string $flag)
 */
class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    /** @inheritdoc */
    protected $fillable = [
        'gender',
        'title_before',
        'title_after',

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
        $columns  = preg_filter('/^/', 'is_', config('lecturize.contacts.flags', ['public', 'primary']));

        $this->fillable(array_merge($fillable, $columns));
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(config('lecturize.addresses.model', Address::class));
    }

    public static function getValidationRules(): array
    {
        return config('lecturize.contacts.rules', []);
    }

    public function getFullNameAttribute(?bool $with_salutation = null, ?bool $with_titles = null, ?bool $with_name_reversed = null): string
    {
        $generator = (new NameGenerator(
            $this->gender,
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->title_before,
            $this->title_after,
        ));

        if ($with_salutation)
            $generator->withSalutation();

        if ($with_titles)
            $generator->withTitles();

        if ($with_name_reversed)
            $generator->withNameReversed();

        return $generator->toString();
    }

    public function getFullNameRevAttribute(?bool $show_salutation = null, ?bool $with_titles = null): string
    {
        return $this->getFullNameAttribute($show_salutation, $with_titles, true);
    }

    public function scopeFlag(Builder $query, string $flag): Builder
    {
        return $query->where('is_'.$flag, true);
    }

    protected static function newFactory(): ContactFactory
    {
        return new ContactFactory();
    }
}
