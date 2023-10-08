<?php

namespace Lecturize\Addresses\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lecturize\Addresses\Models\Address;

/** @mixin Address */
class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,

            'addressee' => $this->getAddresseeLines(),

            'gender'       => $this->gender,
            'titleBefore' => $this->title_before,
            'titleAfter'  => $this->title_after,

            'firstName'  => $this->first_name,
            'middleName' => $this->middle_name,
            'lastName'   => $this->last_name,

            'company' => $this->company,
            'extra'   => $this->extra,

            'route'        => $this->route,
            'streetNumber' => $this->street_number,
            'street'       => $this->street,
            'streetExtra'  => $this->street_extra,
            'postalCode'   => $this->post_code,
            'city'         => $this->city,
            'state'        => $this->state,
            'country'      => new CountryResource($this->country),

            'vatId'        => $this->vat_id,
            'eoriId'       => $this->eori_id,
            'contactPhone' => $this->contact_phone,
            'contactEmail' => $this->contact_email,
            'billingEmail' => $this->billing_email,

            'instructions' => $this->instructions,
            'notes'        => $this->notes,

            'data' => [
                'array'  => $this->getArray(),
                'string' => $this->getLine(),
                'html'   => $this->getHtml(),
            ],
        ];
    }
}
