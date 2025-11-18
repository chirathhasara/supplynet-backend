<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\PurchaseOrder;

class PurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;

    /**
     * Create a new message instance.
    */
    
    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder->load(['rawMaterial', 'supplier']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Purchase Order #' . $this->purchaseOrder->id,
        );
    }

    /**
     * Get the message content definition.
     */
    
    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-order',
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
