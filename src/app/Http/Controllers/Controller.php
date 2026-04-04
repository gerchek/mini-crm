<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Mini CRM API',
    description: 'API for collecting and processing customer feedback tickets',
)]
#[OA\Server(url: 'http://localhost:8080', description: 'Local Docker')]
abstract class Controller
{
}
