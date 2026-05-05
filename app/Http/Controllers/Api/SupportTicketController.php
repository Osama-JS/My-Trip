<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\TicketRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class SupportTicketController extends Controller
{
    #[OA\Get(
        path: "/api/user/tickets",
        summary: "Get Customer Support Tickets",
        operationId: "getTickets",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Tickets retrieved successfully")
        ]
    )]
    public function index()
    {
        $tickets = auth()->user()->supportTickets()->latest()->paginate(10);
        return $this->apiResponse(false, __('Tickets retrieved successfully.'), $tickets);
    }

    #[OA\Post(
        path: "/api/user/tickets",
        summary: "Create a New Support Ticket",
        operationId: "createTicket",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["subject", "category", "priority", "message"],
                properties: [
                    new OA\Property(property: "subject", type: "string"),
                    new OA\Property(property: "category", type: "string"),
                    new OA\Property(property: "priority", type: "string", enum: ["low", "medium", "high", "urgent"]),
                    new OA\Property(property: "message", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Ticket created successfully")
        ]
    )]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');
                $attachments[] = $path;
            }
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'attachments' => $attachments,
        ]);

        return $this->apiResponse(false, __('Ticket created successfully.'), $ticket, null, 201);
    }

    #[OA\Get(
        path: "/api/user/tickets/{id}",
        summary: "Get Ticket Details and Messages",
        operationId: "getTicketDetails",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        responses: [
            new OA\Response(response: 200, description: "Ticket details retrieved successfully")
        ]
    )]
    public function show($id)
    {
        $ticket = auth()->user()->supportTickets()->with(['messages.sender', 'rating'])->findOrFail($id);
        return $this->apiResponse(false, __('Ticket details retrieved successfully.'), $ticket);
    }

    #[OA\Post(
        path: "/api/user/tickets/{id}/reply",
        summary: "Reply to a Support Ticket",
        operationId: "replyToTicket",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["message"],
                properties: [new OA\Property(property: "message", type: "string")]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Reply sent successfully")
        ]
    )]
    public function reply(Request $request, $id)
    {
        $ticket = auth()->user()->supportTickets()->findOrFail($id);

        if ($ticket->status === 'closed') {
            return $this->apiResponse(true, __('You cannot reply to a closed ticket.'), null, null, 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets/attachments', 'public');
                $attachments[] = $path;
            }
        }

        $message = TicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => auth()->id(),
            'message' => $request->message,
            'attachments' => $attachments,
        ]);

        if ($ticket->status === 'pending') {
            $ticket->update(['status' => 'open']);
        }

        return $this->apiResponse(false, __('Reply sent successfully.'), $message, null, 201);
    }

    #[OA\Post(
        path: "/api/user/tickets/{id}/rate",
        summary: "Rate a Closed Support Ticket",
        operationId: "rateTicket",
        tags: ["Support"],
        security: [["bearerAuth" => []]],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["rating"],
                properties: [
                    new OA\Property(property: "rating", type: "integer", minimum: 1, maximum: 5),
                    new OA\Property(property: "comment", type: "string")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Thank you for your feedback!")
        ]
    )]
    public function rate(Request $request, $id)
    {
        $ticket = auth()->user()->supportTickets()->findOrFail($id);

        if ($ticket->status !== 'closed') {
            return $this->apiResponse(true, __('You can only rate closed tickets.'), null, null, 403);
        }

        if ($ticket->rating) {
            return $this->apiResponse(true, __('You have already rated this ticket.'), null, null, 403);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse(true, __('Validation failed.'), $validator->errors(), null, 422);
        }

        $rating = TicketRating::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return $this->apiResponse(false, __('Thank you for your feedback!'), $rating, null, 201);
    }
}
