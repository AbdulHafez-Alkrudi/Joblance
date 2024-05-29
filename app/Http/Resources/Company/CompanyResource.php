<?php

namespace App\Http\Resources\Company;

use App\Models\Major;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->user->id,
            'name'             => $this->name,
            'image'            => $this->image != null ? asset('storage/' . $this->image) : "",
            'description'      => is_null($this->description) ? "" : $this->description,
            'major'            => (new Major)->get_major($this->major_id , request('lang') , true),
            'location'         => $this->location,
            'num_of_employees' => $this->num_of_employees,
        ];
    }
}
