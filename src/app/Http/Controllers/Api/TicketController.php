<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketStatisticsResource;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService
    ) {}

    #[OA\Post(
        path: '/api/tickets',
        summary: 'Create a new ticket',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['name', 'phone', 'email', 'subject', 'body'],
                    properties: [
                        new OA\Property(property: 'name', type: 'string', example: 'John Doe'),
                        new OA\Property(property: 'phone', type: 'string', example: '+12025551234', description: 'E.164 format'),
                        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'john@example.com'),
                        new OA\Property(property: 'subject', type: 'string', example: 'Question about service'),
                        new OA\Property(property: 'body', type: 'string', example: 'Hello, I have a question.'),
                        new OA\Property(property: 'files[]', type: 'array', items: new OA\Items(type: 'string', format: 'binary'), description: 'Attachments (max 5, 10MB each)'),
                    ]
                )
            )
        ),
        tags: ['Tickets'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Ticket created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', ref: '#/components/schemas/Ticket'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'The name field is required.'),
                        new OA\Property(property: 'errors', type: 'object'),
                    ]
                )
            ),
            new OA\Response(
                response: 429,
                description: 'Rate limit exceeded',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'You can only submit one ticket per day.'),
                    ]
                )
            ),
        ]
    )]
    public function store(StoreTicketRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (!$this->ticketService->canSubmitTicket($data['email'], $data['phone'])) {
            return response()->json([
                'message' => 'You can only submit one ticket per day.',
            ], 429);
        }

        $files = $request->file('files', []);
        $ticket = $this->ticketService->createTicket($data, $files);

        return (new TicketResource($ticket))
            ->response()
            ->setStatusCode(201);
    }

    #[OA\Get(
        path: '/api/tickets/statistics',
        summary: 'Get ticket statistics',
        tags: ['Tickets'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Statistics',
                content: new OA\JsonContent(ref: '#/components/schemas/TicketStatistics')
            ),
        ]
    )]
    public function statistics(): TicketStatisticsResource
    {
        $stats = $this->ticketService->getStatistics();

        return new TicketStatisticsResource($stats);
    }
}
