<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
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
        // return parent::toArray($request);

        return [
            // 'id' => $this->id,
            // 'customer_id' => $this->customer_id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => date('Y-m-d', strtotime($this->created_at)),
            'registrations' => $this->registrations,
            'activations' => $this->activations,
            'appointments' => $this->appointments,
            // 'invoice_total' => $this->invoice_total.' SAR',
            'invoice_total' => count($this->invoice_total) > 1 ? max($this->invoice_total).' SAR' : $this->invoice_total[0].' SAR'
        ];
    }
}
