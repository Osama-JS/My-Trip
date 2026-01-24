<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TraveloproService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class FlightController extends Controller
{
    protected $traveloproService;

    public function __construct(TraveloproService $traveloproService)
    {
        $this->traveloproService = $traveloproService;
    }

    #[OA\Post(
        path: "/api/flights/search",
        summary: "Search for flights",
        operationId: "searchFlights",
        description: "Search for flight availability using Travelopro API.",
        tags: ["Flights"],
        parameters: [
            new OA\Parameter(
                name: "Accept-Language",
                in: "header",
                description: "The language of the response (ar, en)",
                required: false,
                schema: new OA\Schema(type: "string", default: "en", enum: ["en", "ar"])
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["journeyType", "OriginDestinationInfo", "class", "adults"],
                properties: [
                    new OA\Property(property: "journeyType", type: "string", enum: ["OneWay", "Return", "Circle", "MultiCity"], example: "OneWay"),
                    new OA\Property(
                        property: "OriginDestinationInfo",
                        type: "array",
                        items: new OA\Items(
                            type: "object",
                            required: ["departureDate", "airportOriginCode", "airportDestinationCode"],
                            properties: [
                                new OA\Property(property: "departureDate", type: "string", format: "date", example: "2024-12-01"),
                                new OA\Property(property: "returnDate", type: "string", format: "date", example: "2024-12-10", description: "Required if journeyType is Return"),
                                new OA\Property(property: "airportOriginCode", type: "string", example: "DXB"),
                                new OA\Property(property: "airportDestinationCode", type: "string", example: "LHR")
                            ]
                        )
                    ),
                    new OA\Property(property: "class", type: "string", enum: ["Economy", "Business", "First", "PremiumEconomy"], example: "Economy"),
                    new OA\Property(property: "adults", type: "integer", example: 1),
                    new OA\Property(property: "childs", type: "integer", example: 0),
                    new OA\Property(property: "infants", type: "integer", example: 0),
                    new OA\Property(property: "airlineCode", type: "string", example: "", description: "Preferred airline code"),
                    new OA\Property(property: "directFlight", type: "boolean", example: false),
                    new OA\Property(property: "requiredCurrency", type: "string", example: "SAR")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful search",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Flights retrieved successfully."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Validation failed."),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Server error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Service unavailable"),
                        new OA\Property(property: "data", type: "object")
                    ]
                )
            )
        ]
    )]
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'journeyType' => 'required|string|in:OneWay,Return,Circle,MultiCity',
            'OriginDestinationInfo' => 'required|array|min:1',
            'OriginDestinationInfo.*.departureDate' => 'required|date',
            'OriginDestinationInfo.*.returnDate' => 'nullable|date|after_or_equal:OriginDestinationInfo.*.departureDate',
            'OriginDestinationInfo.*.airportOriginCode' => 'required|string|size:3',
            'OriginDestinationInfo.*.airportDestinationCode' => 'required|string|size:3',
            'class' => 'required|string',
            'adults' => 'required|integer|min:1',
            'childs' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'airlineCode' => 'nullable|string',
            'directFlight' => 'nullable|boolean',
            'requiredCurrency' => 'nullable|string|size:3',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $result = $this->traveloproService->searchFlights($request->all());

        if (isset($result['status']) && $result['status'] === 'error') {
             // Handle service specific error structure or generic error
             $message = $result['message'] ?? __('Failed to fetch flight data');
             return $this->apiResponse(true, $message, $result, null, 500);
        }

        return $this->apiResponse(false, __('Flights retrieved successfully.'), $result, null, 200);
    }
}
