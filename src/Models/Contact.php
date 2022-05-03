<?php

namespace Kwidoo\Contacts\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kwidoo\Contacts\Collections\ContactCollection;
use Kwidoo\Contacts\Contracts\Item;
use Kwidoo\Contacts\Items\ContactItem;
use Kwidoo\Contacts\Casts\ContactItemCast;
use Spatie\Translatable\HasTranslations;
use Webpatser\Uuid\Uuid;

/**
 *
 * @package Kwidoo\Contacts\Models
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
        'values' => ContactItemCast::class,
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
     * @param array $models
     *
     * @return ContactCollection
     */
    public function newCollection(array $models = [])
    {
        return new ContactCollection($models);
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
