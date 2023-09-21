<?php

namespace Lecturize\Addresses\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lecturize\Addresses\Models\Contact;

/** @mixin Contact */
class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,

            'gender'       => $this->gender,
            'title-before' => $this->title_before,
            'title-after'  => $this->title_after,

            'first-name'  => $this->first_name,
            'middle-name' => $this->middle_name,
            'last-name'   => $this->last_name,

            'company' => $this->company,
            'extra'   => $this->extra,

            'position'      => $this->position,
            'phone'         => $this->phone,
            'mobile'        => $this->mobile,
            'fax'           => $this->fax,
            'email'         => $this->email,
            'email_invoice' => $this->email_invoice,
            'website'       => $this->website,

            'vat-id' => $this->vat_id,

            'notes' => $this->notes,

            'full-name'     => $this->full_name,
            'full-name-rev' => $this->full_name_rev,
        ];
    }
}
