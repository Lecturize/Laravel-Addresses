<?php

namespace Kwidoo\Contacts\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kwidoo\Contacts\Collections\ContactItemCollection;
use Kwidoo\Contacts\Contracts\Item;
use Kwidoo\Contacts\Items\ContactItem;
use Spatie\Translatable\HasTranslations;
use Webpatser\Uuid\Uuid;

/**
 *
 * @package Kwidoo\Contacts\Models
 *
 * @method Collection<ContactItem> addEmailValue(string $data)
 * @method Collection<ContactItem> addPhoneValue(string $data)
 * @method Collection<ContactItem> addMobileValue(string $data)
 * @method Collection<ContactItem> addAddressValue(string $data)
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $company
 * @property string $vat_id
 *
 */
class Contact extends Model
{
    use HasTranslations;

    public const DEFAULT_TYPE = 'email';

    use SoftDeletes;

    /** @inheritdoc */
    protected $fillable = [
        'gender',
        'title',
        'first_name',
        'middle_name',
        'last_name',

        'company',
        'vat_id',
        'extra',
        'position',
        'values',

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
        'values' => 'json',
        'properties' => 'array',
    ];

    /**
     * @var array
     */
    public $translatable = ['title', 'first_name', 'middle_name', 'last_name'];

    /** @inheritdoc */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('contacts.tables.main', 'contacts');
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
     * Get the related model.
     *
     * @return MorphTo
     */
    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return ContactItem[]|Collection
     */
    public function getValuesAttribute(): Collection
    {
        if (!array_key_exists('values', $this->attributes)) {
            return  new ContactItemCollection;
        }
        return ContactItem::newCollection($this->attributes['values']);
    }

    /**
     * @param array $attributes
     *
     * @return Collection
     */
    public function addValue(array $attributes): Collection
    {
        $values = $this->values;
        $this->values = $values->push(new ContactItem((object)$attributes));

        return $this->values; //$this->getValuesAttribute();
    }

    /**
     * @param Item|string $item
     *
     * @return Collection
     */
    public function removeValue($item): Collection
    {
        $value = $this->values->findWithKey($item);
        if (!empty($value)) {
            $key = array_keys($value)[0];
            // $this->values =
            $this->values->forget($key);
        }

        return $this->getValuesAttribute();
    }

    public function __call($method, $parameters)
    {
        $name = Str::lower(str_replace('Value', '', str_replace('add', '', $method)));
        if (in_array($name, config('contacts.value_types'))) {
            $value = $parameters[0];
            if (is_array($parameters[0])) {
                if (!array_key_exists($name, $parameters[0])) {
                    throw new Exception('Wrong parameter type');
                }
                $value = $parameters[0][$name];
            }
            return $this->addValue([
                'type' => $name,
                'value' => $value
            ]);
        }
        return parent::__call($method, $parameters);
    }
}
