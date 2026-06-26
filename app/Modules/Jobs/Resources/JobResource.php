<?php

namespace App\Modules\Jobs\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => is_object($this->status) ? $this->status->value : $this->status,
            'organization_id' => $this->organization_id,
            'due_at' => $this->due_at ? $this->due_at->toIso8601String() : null,
            'is_notified' => (bool) $this->is_notified,
            'created_by' => $this->whenLoaded('creator', fn() => $this->creator->name),
            'updated_by' => $this->whenLoaded('editor', fn() => $this->editor->name),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
