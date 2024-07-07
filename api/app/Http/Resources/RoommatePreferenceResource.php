<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoommatePreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request): array
    {
        return [
            'preference_set' => true,
            'student_id' => $this->student_id,
            'question_1' => $this->question_1,
            'question_2' => $this->question_2,
            // 'question_3' => $this->question_3,
        ];
    }
}
