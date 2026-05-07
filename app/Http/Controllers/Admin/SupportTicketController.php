<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    public function index(Request $request)
    {
        $query = SupportTicket::with(['user', 'assignedAdmin'])->latest();

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && !empty($request->priority)) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('search') && !empty($request->search)) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($uq) use ($request) {
                      $uq->where('first_name', 'like', '%' . $request->search . '%')
                         ->orWhere('last_name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $tickets = $query->paginate(15);

        return view('admin.support.index', compact('tickets'));
    }

    public function show($id)
    {
        $ticket = SupportTicket::with(['user', 'messages.sender', 'rating'])->findOrFail($id);
        
        // Mark as pending if it was open and admin is viewing it (optional logic)
        // if ($ticket->status === 'open') {
        //     $ticket->update(['status' => 'pending', 'assigned_to' => auth()->id()]);
        // }

        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ]);

        $ticket = SupportTicket::findOrFail($id);

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

        // If ticket was closed, re-opening it is usually not standard for admins unless explicit
        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'pending', 'assigned_to' => auth()->id()]);
        }

        // Send notification to user
        try {
            $title = __('رد جديد على تذكرتك');
            $body = __('تم الرد على تذكرتك: ') . $ticket->subject;
            $this->notificationService->sendToUser(
                $ticket->user,
                Notification::TYPE_SUPPORT_REPLY,
                $title,
                $body,
                ['ticket_id' => $ticket->id]
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send support reply notification: " . $e->getMessage());
        }

        return back()->with('success', __('Reply sent successfully.'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,pending,closed',
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['status' => $request->status]);

        return back()->with('success', __('Ticket status updated to ' . $request->status));
    }

    public function assign(Request $request, $id)
    {
        $ticket = SupportTicket::findOrFail($id);
        $ticket->update(['assigned_to' => auth()->id()]);

        return back()->with('success', __('Ticket assigned to you.'));
    }
}
