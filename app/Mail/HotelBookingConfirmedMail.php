<?php

namespace App\Mail;

use App\Models\HotelBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class HotelBookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public HotelBooking $booking;
    public ?string $voucherRelativePath;

    /**
     * Create a new message instance.
     */
    public function __construct(HotelBooking $booking, ?string $voucherRelativePath = null)
    {
        $this->booking = $booking;
        $this->voucherRelativePath = $voucherRelativePath ?: $booking->invoice_path;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ref = $this->booking->supplier_confirmation_num ?: ($this->booking->reference_num ?: '#' . $this->booking->id);
        $subject = 'Booking Confirmed: ' . $this->booking->hotel_name . ' (Ref: ' . $ref . ')';

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
            view: 'emails.hotel_booking_confirmed',
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
            $fileName = 'Hotel-Voucher-' . ($this->booking->reference_num ?: $this->booking->id) . '.pdf';

            $attachments[] = Attachment::fromPath($fullPath)
                ->as($fileName)
                ->withMime('application/pdf');
        }

        return $attachments;
    }
}
