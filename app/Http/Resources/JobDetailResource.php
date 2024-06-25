<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray( $request): array
    {
        return [
            'id'                        => $this->id,
            'job_type_id'               => $this->job_type_id,
            'experience_level_id'       => $this->experience_level_id,
            'remote_id'                 => $this->remote_id,
            'major_id'                  => $this->major_id,
            'company_id'                => $this->company_id,
            'title'                     => $this->title,
            'location'                  => $this->location,
            'about_job'                 => $this->about_job,
            'requirements'              => $this->requirements,
            'additional_information'    => $this->additional_information,
            'show_number_of_employees'  => $this->show_number_of_employees,
            'show_about_the_company'    => $this->show_about_the_company,
            'show_in_important_jobs'    => $this->show_in_important_jobs,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at
        ];
    }
}
