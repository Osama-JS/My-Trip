<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "My-Trip API Documentation",
    version: "1.0.0",
    description: "API documentation for My-Trip Platform",
    contact: new OA\Contact(email: "support@mytrip.com")
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "Development Server"
)]
#[OA\Server(
    url: "https://flyvio.net",
    description: "Production Server"
)]
#[OA\Server(
    url: "http://localhost/my-trip/public",
    description: "Local Server (XAMPP)"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Use a token to access protected routes"
)]
abstract class Controller
{
    use \App\Traits\ApiResponseTrait;
}
