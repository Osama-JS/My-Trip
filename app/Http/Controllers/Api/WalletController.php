<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class WalletController extends Controller
{
    #[OA\Get(
        path: "/api/user/wallet",
        summary: "Get Customer Wallet Details",
        operationId: "getWalletDetails",
        description: "Returns the authenticated customer's wallet balance and currency.",
        tags: ["Wallet"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Wallet details retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Wallet details retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "balance", type: "number", format: "float", example: 150.50),
                            new OA\Property(property: "total_credit", type: "number", format: "float", example: 500.00),
                            new OA\Property(property: "total_debit", type: "number", format: "float", example: 349.50),
                            new OA\Property(property: "currency", type: "string", example: "SAR"),
                            new OA\Property(property: "status", type: "string", example: "active")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function getWallet(Request $request)
    {
        $wallet = app(\App\Services\WalletService::class)->getOrCreateWallet($request->user());

        $totalCredit = $wallet->transactions()->where('type', 'credit')->sum('amount');
        $totalDebit = $wallet->transactions()->where('type', 'debit')->sum('amount');

        return response()->json([
            'error' => false,
            'message' => __('Wallet details retrieved successfully.'),
            'data' => [
                'balance' => (float) $wallet->balance,
                'total_credit' => (float) $totalCredit,
                'total_debit' => (float) $totalDebit,
                'currency' => $wallet->currency,
                'status' => $wallet->status,
            ]
        ]);
    }

    #[OA\Get(
        path: "/api/user/wallet/transactions",
        summary: "Get Wallet Transactions History",
        operationId: "getWalletTransactions",
        description: "Returns a paginated list of the customer's wallet transactions with search and filter support.",
        tags: ["Wallet"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "page",
                in: "query",
                description: "Page number for pagination",
                required: false,
                schema: new OA\Schema(type: "integer", default: 1)
            ),
            new OA\Parameter(
                name: "type",
                in: "query",
                description: "Filter by transaction type (credit or debit)",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["credit", "debit"])
            ),
            new OA\Parameter(
                name: "search",
                in: "query",
                description: "Search in transaction description",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Transactions retrieved successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "boolean", example: false),
                        new OA\Property(property: "message", type: "string", example: "Transactions retrieved successfully."),
                        new OA\Property(property: "data", type: "object", properties: [
                            new OA\Property(property: "transactions", type: "object", properties: [
                                new OA\Property(property: "current_page", type: "integer", example: 1),
                                new OA\Property(property: "data", type: "array", items: new OA\Items(
                                    properties: [
                                        new OA\Property(property: "id", type: "integer", example: 101),
                                        new OA\Property(property: "wallet_id", type: "integer", example: 1),
                                        new OA\Property(property: "amount", type: "number", format: "float", example: 50.00),
                                        new OA\Property(property: "type", type: "string", example: "credit"),
                                        new OA\Property(property: "balance_before", type: "number", format: "float", example: 100.00),
                                        new OA\Property(property: "balance_after", type: "number", format: "float", example: 150.00),
                                        new OA\Property(property: "description", type: "string", example: "Refund for booking #12345"),
                                        new OA\Property(property: "reference_id", type: "integer", example: 12345),
                                        new OA\Property(property: "reference_type", type: "string", example: "App\\Models\\FlightBooking"),
                                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2024-05-01T12:00:00Z")
                                    ]
                                )),
                                new OA\Property(property: "total", type: "integer", example: 50),
                                new OA\Property(property: "per_page", type: "integer", example: 15)
                            ])
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated")
        ]
    )]
    public function getTransactions(Request $request)
    {
        $wallet = app(\App\Services\WalletService::class)->getOrCreateWallet($request->user());

        $query = $wallet->transactions()->orderBy('created_at', 'desc');

        // Filtering by type
        if ($request->has('type') && in_array($request->type, ['credit', 'debit'])) {
            $query->where('type', $request->type);
        }

        // Searching in description
        if ($request->has('search') && !empty($request->search)) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $transactions = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'error' => false,
            'message' => __('Transactions retrieved successfully.'),
            'data' => [
                'transactions' => $transactions
            ]
        ]);
    }
}
