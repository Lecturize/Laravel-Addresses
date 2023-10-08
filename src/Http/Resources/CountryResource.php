<?php

namespace Lecturize\Addresses\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lecturize\Addresses\Models\Country;

/** @mixin Country */
class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'iso_2' => $this->iso_3166_2,
            'iso_3' => $this->iso_3166_3,

            'countryCode'  => $this->country_code,
            'callingCode'  => $this->calling_code,
            'currencyCode' => $this->currency_code,

            'fullName' => $this->full_name,
            'name'     => $this->name,
        ];
    }
}
