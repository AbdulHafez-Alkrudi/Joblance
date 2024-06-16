<?php

namespace App\Http\Resources\Freelancer;

use App\Models\Users\Freelancer\Freelancer;
use App\Models\Users\Major;
use App\Models\Users\Freelancer\StudyCase;
use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->user->id,
            'first_name'   => $this->first_name,
            'last_name'    => $this->last_name,
            'image'        => ($this->image != null ? asset('storage/' . $this->image) : ""),
            'email'        => $this->email,
            "phone_number" => $this->phone_number,
            'bio'          => $this->bio,
            'major'        => (new Major)->get_major($this->major_id , request('lang') , true), // Assuming you have a Major resource
            'location'     => $this->location,
            'study_case'   => (new StudyCase)->get_study_case($this->study_case_id , request('lang') , true), // Adjust if you have a relationship
            'open_to_work' => $this->open_to_work,
            'counter'      => $this->counter,
            'rate'         => (new Freelancer)->rate($this->sum_rate, $this->counter),
        ];
    }
}
