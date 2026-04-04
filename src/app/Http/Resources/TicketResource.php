<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'Ticket',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'subject', type: 'string', example: 'Question about service'),
        new OA\Property(property: 'body', type: 'string', example: 'Hello, I have a question.'),
        new OA\Property(property: 'status', type: 'string', enum: ['new', 'in_progress', 'processed'], example: 'new'),
        new OA\Property(property: 'responded_at', type: 'string', format: 'date-time', nullable: true),
        new OA\Property(property: 'customer', ref: '#/components/schemas/Customer'),
        new OA\Property(property: 'files', type: 'array', items: new OA\Items(ref: '#/components/schemas/Media')),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ]
)]
class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'body' => $this->body,
            'status' => $this->status,
            'responded_at' => $this->responded_at?->toIso8601String(),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'files' => MediaResource::collection($this->whenLoaded('media')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
