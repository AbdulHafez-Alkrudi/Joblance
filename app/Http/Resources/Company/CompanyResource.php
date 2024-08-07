<?php

namespace App\Http\Resources\Company;

use App\Models\Users\Major;
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
            'email'            => $this->user->email,
            "phone_number"     => $this->user->phone_number,
            'description'      => is_null($this->description) ? "" : $this->description,
            'major'            => (new Major)->get_major($this->major_id , request('lang') , true),
            'location'         => $this->location,
            'num_of_employees' => $this->num_of_employees,
            'followers'        => count($this->user->followers),
            'evaluated'        => $this->user->hasEvaluated($this->user),
            'followed'         => $this->user->hasFollow($this->user)
        ];
    }
}
