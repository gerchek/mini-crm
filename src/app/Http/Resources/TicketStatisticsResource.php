<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TicketStatistics',
    properties: [
        new OA\Property(property: 'today', type: 'integer', example: 5, description: 'Tickets created today'),
        new OA\Property(property: 'week', type: 'integer', example: 12, description: 'Tickets created this week'),
        new OA\Property(property: 'month', type: 'integer', example: 34, description: 'Tickets created this month'),
    ]
)]
class TicketStatisticsResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'today' => $this->resource['today'],
            'week' => $this->resource['week'],
            'month' => $this->resource['month'],
        ];
    }
}
