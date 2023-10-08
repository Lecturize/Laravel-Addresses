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
            'titleBefore' => $this->title_before,
            'titleAfter'  => $this->title_after,

            'firstName'  => $this->first_name,
            'middleName' => $this->middle_name,
            'lastName'   => $this->last_name,

            'company' => $this->company,
            'extra'   => $this->extra,

            'position'     => $this->position,
            'phone'        => $this->phone,
            'mobile'       => $this->mobile,
            'fax'          => $this->fax,
            'email'        => $this->email,
            'emailInvoice' => $this->email_invoice,
            'website'      => $this->website,

            'vatId' => $this->vat_id,

            'notes' => $this->notes,

            'name' => [
                'full'     => $this->full_name,
                'reversed' => $this->full_name_rev,
            ],
        ];
    }
}
