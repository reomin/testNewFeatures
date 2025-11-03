<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //見せたくないカラムを除去できる
        return [
            'id' => $this->id,
            'title' => $this->title,
            'completed' => $this->completed,
            'user_id' => $this->user_id,
        ];
    }
}
