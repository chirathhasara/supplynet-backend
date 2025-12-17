<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-section {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-good {
            background-color: #d4edda;
            color: #155724;
        }
        .status-damaged {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-expired {
            background-color: #fff3cd;
            color: #856404;
        }
        .alert-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-box.error {
            background-color: #f8d7da;
            border-color: #dc3545;
        }
        .alert-box h3 {
            margin-top: 0;
            color: #856404;
        }
        .alert-box.error h3 {
            color: #721c24;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #777;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>📦 Purchase Order Received</h1>
        </div>

        <p>Dear <strong>{{ $supplier->name }}</strong>,</p>

        <p>This email confirms that we have received your purchase order delivery. Below are the details:</p>

        <div class="info-section">
            <h3 style="margin-top: 0; color: #4CAF50;">Order Information</h3>
            <div class="info-row">
                <span class="label">Purchase Order ID:</span>
                <span class="value">#{{ $purchaseOrder->id }}</span>
            </div>
            <div class="info-row">
                <span class="label">Raw Material:</span>
                <span class="value">{{ $rawMaterial->name }}</span>
            </div>
            <div class="info-row">
                <span class="label">Received Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($receivedOrder->received_date)->format('M d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="label">Due Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($receivedOrder->due_date)->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3 style="margin-top: 0; color: #4CAF50;">Delivery Details</h3>
            <div class="info-row">
                <span class="label">Requested Units:</span>
                <span class="value">{{ number_format($receivedOrder->requested_units, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="label">Received Units:</span>
                <span class="value">{{ number_format($receivedOrder->received_units, 2) }}</span>
            </div>
            @if($receivedOrder->unit_shortage > 0)
            <div class="info-row">
                <span class="label">Unit Shortage:</span>
                <span class="value" style="color: #dc3545; font-weight: bold;">{{ number_format($receivedOrder->unit_shortage, 2) }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="label">Quality Status:</span>
                <span class="value">
                    @if($receivedOrder->quality_status === 'good')
                        <span class="status-badge status-good">✓ Good</span>
                    @elseif($receivedOrder->quality_status === 'damaged')
                        <span class="status-badge status-damaged">⚠ Damaged</span>
                    @elseif($receivedOrder->quality_status === 'expired')
                        <span class="status-badge status-expired">⏰ Expired</span>
                    @endif
                </span>
            </div>
            @if($receivedOrder->batch_no)
            <div class="info-row">
                <span class="label">Batch Number:</span>
                <span class="value">{{ $receivedOrder->batch_no }}</span>
            </div>
            @endif
            @if($receivedOrder->expiry_date)
            <div class="info-row">
                <span class="label">Expiry Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($receivedOrder->expiry_date)->format('M d, Y') }}</span>
            </div>
            @endif
        </div>

        @if($receivedOrder->no_of_late_days > 0)
        <div class="alert-box error">
            <h3>⚠️ Delivery Delay Notice</h3>
            <p>This order was delivered <strong>{{ $receivedOrder->no_of_late_days }} day(s)</strong> late.</p>
        </div>
        @endif

        @if($receivedOrder->unit_shortage > 0)
        <div class="alert-box error">
            <h3>📊 Shortage Alert</h3>
            <p>There is a shortage of <strong>{{ number_format($receivedOrder->unit_shortage, 2) }} units</strong>.</p>
            @if($receivedOrder->loss_amount > 0)
            <p>Estimated loss amount: <strong>Rs. {{ number_format($receivedOrder->loss_amount, 2) }}</strong></p>
            @endif
            @if($receivedOrder->difference_reason)
            <p><strong>Reason:</strong> {{ $receivedOrder->difference_reason }}</p>
            @endif
        </div>
        @endif

        @if($receivedOrder->quality_status && in_array($receivedOrder->quality_status, ['damaged', 'expired']))
        <div class="alert-box error">
            <h3>❌ Quality Issue Detected</h3>
            <p>The received items have been marked as <strong>{{ ucfirst($receivedOrder->quality_status) }}</strong>.</p>
        </div>
        @endif

        @if($receivedOrder->note)
        <div class="info-section">
            <h3 style="margin-top: 0; color: #4CAF50;">Additional Notes</h3>
            <p>{{ $receivedOrder->note }}</p>
        </div>
        @endif

        @if($receivedOrder->no_of_late_days == 0 && $receivedOrder->unit_shortage == 0 && $receivedOrder->quality_status === 'good')
        <div class="alert-box" style="background-color: #d4edda; border-color: #28a745;">
            <h3 style="color: #155724;">✅ Perfect Delivery!</h3>
            <p>Thank you for delivering on time with excellent quality. We appreciate your reliability!</p>
        </div>
        @endif

        <p style="margin-top: 30px;">If you have any questions or concerns about this received order, please contact us.</p>

        <p>Best regards,<br>
        <strong>Supply Chain Management Team</strong></p>

        <div class="footer">
            <p>This is an automated email notification. Please do not reply directly to this email.</p>
            <p>&copy; {{ date('Y') }} Supply Chain Management System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
