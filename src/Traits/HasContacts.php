<?php

namespace Lecturize\Addresses\Traits;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

use Lecturize\Addresses\Models\Contact;
use Lecturize\Addresses\Exceptions\FailedValidationException;

/**
 * Class HasContacts
 * @package Lecturize\Addresses\Traits
 * @property Collection|Contact[]  $contacts
 */
trait HasContacts
{
    public function __call($method, $parameters)
    {
        $available_flags = config('lecturize.contacts.flags');
        $available_flags = array_map(function ($flag) {
            return Str::ucfirst($flag);
        }, $available_flags);

        if (preg_match('/^get(' . implode('|', $available_flags) . ')Contact$/', $method, $matches)) {
            $flag = strtolower($matches[1]);

            return $this->getContactByFlag($flag);
        }

        return parent::__call($method, $parameters);
    }

    public function contacts(): MorphMany
    {
        /** @var Model $this */
        return $this->morphMany(Contact::class, 'contactable');
    }

    public function hasContacts(): bool
    {
        return $this->contacts->isNotEmpty();
    }

    /** @throws Exception */
    public function addContact(array $attributes): Contact|Model
    {
        $attributes = $this->loadContactAttributes($attributes);

        return $this->contacts()->updateOrCreate($attributes);
    }

    /** @throws Exception */
    public function updateContact(Contact $contact, array $attributes): bool
    {
        $attributes = $this->loadContactAttributes($attributes);

        return $contact->fill($attributes)->save();
    }

    /** @throws Exception */
    public function deleteContact(Contact $contact): bool
    {
        if ($this !== $contact->contactable()->first())
            return false;

        return $contact->delete();
    }

    public function flushContacts(): bool
    {
        return $this->contacts()->delete();
    }

    protected function getContactByFlag(?string $flag = null, string $direction = 'desc'): ?Contact
    {
        if (! $this->hasContacts()) {
            return null;
        }

        if ($flag !== null) {
            $search_flag = 'is_' . $flag;
            $contact = $this->contacts()
                ->where($search_flag, true)
                ->orderBy('created_at', $direction)
                ->first();

            if ($contact !== null) {
                return $contact;
            }

            /**
             * use the array order of config lecturize.contacts.flags to build up
             * a fallback solution for when no contact with the given flag exists
             */
            $fallback_order = config('lecturize.contacts.flags', []);

            /**
             * fallback order is an array of flags like: ['public', 'primary', 'billing', 'shipping']
             * when calling getBillingAddress() and no address with the billing flag exists, the next earliest flag is used
             * in this case, the flag 'primary' would be used
             */
            $current_flag_index = array_search($flag, $fallback_order);
            $try_flag = $fallback_order[$current_flag_index - 1] ?? null;

            if ($try_flag !== null) {
                $contact = $this->getContactByFlag($try_flag, $direction);

                if ($contact !== null) {
                    return $contact;
                }
            }
        }

        /**
         * should the default fallback logic fail, try to get the first or last contact
         */
        if (! $contact && $direction === 'desc') {
            return $this->contacts()->first();
        } elseif (! $contact && $direction === 'asc') {
            return $this->contacts()->last();
        }

        return null;
    }

    /** @throws FailedValidationException */
    public function loadContactAttributes(array $attributes): array
    {
        // run validation
        $validator = $this->validateContact($attributes);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $error  = '[Addresses] '. implode(' ', $errors);

            throw new FailedValidationException($error);
        }

        return $attributes;
    }

    function validateContact(array $attributes): Validator
    {
        $rules = Contact::getValidationRules();

        return validator($attributes, $rules);
    }
}
