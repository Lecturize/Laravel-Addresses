<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lecturize\Addresses\Models\Country;

/** @mixin Country */
class CountryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'iso-2' => $this->iso_3166_2,
            'iso-3' => $this->iso_3166_3,

            'country-code'  => $this->country_code,
            'calling-code'  => $this->calling_code,
            'currency-code' => $this->currency_code,

            'full-name' => $this->full_name,
            'name'      => $this->name,
        ];
    }
}
