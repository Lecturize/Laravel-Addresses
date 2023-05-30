<?php

namespace Lecturize\Addresses\Traits;

use Exception;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Collection;

use Lecturize\Addresses\Models\Contact;
use Lecturize\Addresses\Exceptions\FailedValidationException;

/**
 * Class HasContacts
 * @package Lecturize\Addresses\Traits
 * @property Collection|Contact[]  $contacts
 */
trait HasContacts
{
    public function contacts(): MorphMany
    {
        /** @var Model $this */
        return $this->morphMany(config('lecturize.contacts.model'), 'contactable');
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
        $rules = config('lecturize.contacts.model')::getValidationRules();

        return validator($attributes, $rules);
    }
}
