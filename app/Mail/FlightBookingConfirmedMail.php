<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class FlightBookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Booking $booking;
    public ?string $voucherRelativePath;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, ?string $voucherRelativePath = null)
    {
        $this->booking = $booking;
        $this->voucherRelativePath = $voucherRelativePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ref = $bookingRef = $this->booking->pnr_code ?: ($this->booking->booking_reference ?: '#' . $this->booking->id);
        $subject = 'Flight Booking Confirmed & E-Tickets Issued (Ref: ' . $ref . ')';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.flight_booking_confirmed',
            with: [
                'booking' => $this->booking,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->voucherRelativePath && Storage::disk('public')->exists($this->voucherRelativePath)) {
            $fullPath = Storage::disk('public')->path($this->voucherRelativePath);
            $fileName = 'Flight-ETicket-' . ($this->booking->pnr_code ?: ($this->booking->booking_reference ?: $this->booking->id)) . '.pdf';

            $attachments[] = Attachment::fromPath($fullPath)
                ->as($fileName)
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
