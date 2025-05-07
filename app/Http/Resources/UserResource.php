<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user_name ?? 'NA',
            'email' => $this->email ?? 'NA',
            'first_name' => $this->first_name ?? 'NA',
            'last_name' => $this->last_name ?? 'NA',
            'other_name' => $this->other_name ?? 'NA',
            'phone_no' => $this->phone_no ?? 'NA',
            'avatar' => $this->avatar ? Storage::url($this->avatar) : 'NA',
            'status' => $this->status ? 'Active' : 'Inactive',
            'email_verified_at' => $this->email_verified_at?->toISOString() ?? 'NA',
            'is_2fa_enabled' => $this->is_2fa_enabled ?? false,
            'created_at' => $this->created_at?->toISOString() ?? 'NA',
            'updated_at' => $this->updated_at?->toISOString() ?? 'NA',
        ];
    }
}
