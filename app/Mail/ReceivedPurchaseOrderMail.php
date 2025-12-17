<?php

namespace App\Mail;

use App\Models\ReceivedPurchaseOrder;
use App\Models\Supplier;
use App\Models\RawMaterial;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReceivedPurchaseOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $receivedOrder;
    public $supplier;
    public $rawMaterial;
    public $purchaseOrder;

    /**
     * Create a new message instance.
     */
    public function __construct(ReceivedPurchaseOrder $receivedOrder, Supplier $supplier, RawMaterial $rawMaterial)
    {
        $this->receivedOrder = $receivedOrder;
        $this->supplier = $supplier;
        $this->rawMaterial = $rawMaterial;
        $this->purchaseOrder = $receivedOrder->purchaseOrder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Purchase Order Received - Order #' . $this->purchaseOrder->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.received-purchase-order',
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
