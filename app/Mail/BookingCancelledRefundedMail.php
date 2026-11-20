<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancelledRefundedMail extends Mailable
{
    use Queueable, SerializesModels;

    public mixed $booking;
    public float $refundAmount;
    public float $penalty;
    public ?float $newWalletBalance;
    public string $bookingType;
    public ?string $reason;

    /**
     * Create a new message instance.
     *
     * @param mixed $booking Booking or HotelBooking instance
     * @param float $refundAmount Amount credited to wallet
     * @param float $penalty Cancellation fee/charge
     * @param float|null $newWalletBalance Updated wallet balance
     * @param string $bookingType 'flight' or 'hotel'
     * @param string|null $reason Cancellation reason
     */
    public function __construct(
        mixed $booking,
        float $refundAmount,
        float $penalty = 0.0,
        ?float $newWalletBalance = null,
        string $bookingType = 'flight',
        ?string $reason = null
    ) {
        $this->booking = $booking;
        $this->refundAmount = $refundAmount;
        $this->penalty = $penalty;
        $this->newWalletBalance = $newWalletBalance;
        $this->bookingType = $bookingType;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $ref = $this->bookingType === 'hotel'
            ? ($this->booking->supplier_confirmation_num ?? ($this->booking->reference_num ?? '#' . $this->booking->id))
            : ($this->booking->pnr_code ?: ($this->booking->booking_reference ?: '#' . $this->booking->id));

        $typeLabelAr = $this->bookingType === 'hotel' ? 'حجز الفندق' : 'حجز الطيران';
        $typeLabelEn = $this->bookingType === 'hotel' ? 'Hotel Booking' : 'Flight Booking';

        $subject = "تم إلغاء {$typeLabelAr} واسترداد المبلغ إلى المحفظة ({$ref}) - {$typeLabelEn} Cancelled & Refunded";

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
            view: 'emails.booking_cancelled_refunded',
            with: [
                'booking' => $this->booking,
                'refundAmount' => $this->refundAmount,
                'penalty' => $this->penalty,
                'newWalletBalance' => $this->newWalletBalance,
                'bookingType' => $this->bookingType,
                'reason' => $this->reason,
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
        return [];
    }
}
