<?php namespace Lecturize\Addresses\Traits;

use Lecturize\Addresses\Models\Contact;
use Lecturize\Addresses\Exceptions\FailedValidationException;

/**
 * Class HasContacts
 * @package Lecturize\Addresses\Traits
 */
trait HasContacts
{
    /**
     * Get all contacts for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }

    /**
     * Check if model has contacts.
     *
     * @return bool
     */
    public function hasContacts()
    {
        return (bool) $this->contacts()->count();
    }

    /**
     * Add a contact to this model.
     *
     * @param  array  $attributes
     * @return mixed
     *
     * @throws \Exception
     */
    public function addContact(array $attributes)
    {
        $attributes = $this->loadContactAttributes($attributes);

        return $this->contacts()->updateOrCreate($attributes);
    }

    /**
     * Updates the given contact.
     *
     * @param  Contact  $contact
     * @param  array    $attributes
     * @return bool
     *
     * @throws \Exception
     */
    public function updateContact(Contact $contact, array $attributes)
    {
        $attributes = $this->loadContactAttributes($attributes);

        return $contact->fill($attributes)->save();
    }

    /**
     * Deletes given contact.
     *
     * @param  Contact  $contact
     * @return bool
     *
     * @throws \Exception
     */
    public function deleteContact(Contact $contact)
    {
        if ($this !== $contact->contactable()->first())
            return false;

        return $contact->delete();
    }

    /**
     * Deletes all the contacts of this model.
     *
     * @return bool
     */
    public function flushContacts()
    {
        return $this->contacts()->delete();
    }

    /**
     * Add country id to attributes array.
     *
     * @param  array  $attributes
     * @return array
     * @throws FailedValidationException
     */
    public function loadContactAttributes(array $attributes)
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

    /**
     * Validate the contact.
     *
     * @param  array  $attributes
     * @return \Illuminate\Contracts\Validation\Validator
     */
    function validateContact(array $attributes)
    {
        $rules = Contact::getValidationRules();

        return validator($attributes, $rules);
    }
}