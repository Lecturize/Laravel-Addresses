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

            'address' => $this->getAddresseeLines(),

            'gender'       => $this->gender,
            'title-before' => $this->title_before,
            'title-after'  => $this->title_after,

            'first-name'  => $this->first_name,
            'middle-name' => $this->middle_name,
            'last-name'   => $this->last_name,

            'company' => $this->company,
            'extra'   => $this->extra,

            'route'         => $this->route,
            'street-number' => $this->street_number,
            'street'        => $this->street,
            'street-extra'  => $this->street_extra,
            'city'          => $this->city,
            'state'         => $this->state,
            'post-code'     => $this->post_code,
            'country'       => (new CountryResource($this->country))->toArray($request),

            'vat-id'        => $this->vat_id,
            'eori-id'       => $this->eori_id,
            'contact-phone' => $this->contact_phone,
            'contact-email' => $this->contact_email,
            'billing-email' => $this->billing_email,

            'instructions' => $this->instructions,
            'notes'        => $this->notes,
        ];
    }
}
