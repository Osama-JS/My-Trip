<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\TicketRating;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->supportTickets()->latest()->paginate(10);
        return view('frontend.customer.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('frontend.customer.support.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ]);

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

        return redirect()->route('customer.support.show', $ticket->id)->with('success', __('Ticket created successfully.'));
    }

    public function show($id)
    {
        $ticket = auth()->user()->supportTickets()->with(['messages.sender', 'rating'])->findOrFail($id);
        return view('frontend.customer.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $ticket = auth()->user()->supportTickets()->findOrFail($id);

        if ($ticket->status === 'closed') {
            return back()->with('error', __('You cannot reply to a closed ticket.'));
        }

        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
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

        // If ticket was pending (answered by admin), it goes back to open/pending status
        // Usually, we keep it as pending or mark as open for admin to see new reply
        if ($ticket->status === 'pending') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', __('Reply sent successfully.'));
    }

    public function rate(Request $request, $id)
    {
        $ticket = auth()->user()->supportTickets()->findOrFail($id);

        if ($ticket->status !== 'closed') {
            return back()->with('error', __('You can only rate closed tickets.'));
        }

        if ($ticket->rating) {
            return back()->with('error', __('You have already rated this ticket.'));
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        TicketRating::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', __('Thank you for your feedback!'));
    }
}
