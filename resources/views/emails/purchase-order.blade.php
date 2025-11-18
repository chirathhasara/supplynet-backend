<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .order-info {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
        }
        .order-info h2 {
            margin-top: 0;
            color: #667eea;
            font-size: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .total-section {
            background: #667eea;
            color: white;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            text-align: right;
        }
        .total-section .total-amount {
            font-size: 24px;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📦 Purchase Order</h1>
            <p style="margin: 5px 0 0 0; opacity: 0.9;">Order #{{ $purchaseOrder->id }}</p>
        </div>

        <div class="content">
            <p>Dear {{ $purchaseOrder->supplier->name }},</p>
            <p>We are pleased to place the following purchase order with you:</p>

            <div class="order-info">
                <h2>Order Details</h2>
                <div class="detail-row">
                    <span class="label">Order Date:</span>
                    <span class="value">{{ date('F d, Y', strtotime($purchaseOrder->date)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Due Date:</span>
                    <span class="value">{{ date('F d, Y', strtotime($purchaseOrder->due_date)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Raw Material:</span>
                    <span class="value">{{ $purchaseOrder->rawMaterial->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="label">Quantity:</span>
                    <span class="value">{{ number_format($purchaseOrder->quantity, 2) }} units</span>
                </div>
                <div class="detail-row">
                    <span class="label">Unit Price:</span>
                    <span class="value">${{ number_format($purchaseOrder->unit_price, 2) }}</span>
                </div>
            </div>

            <div class="total-section">
                <div style="font-size: 16px; margin-bottom: 5px;">Total Amount</div>
                <div class="total-amount">${{ number_format($purchaseOrder->total_price, 2) }}</div>
            </div>

            <p style="margin-top: 30px;">Please confirm receipt of this order and provide an estimated delivery date.</p>
            <p>If you have any questions, please don't hesitate to contact us.</p>
        </div>

        <div class="footer">
            <p><strong>Thank you for your business!</strong></p>
            <p style="margin: 5px 0;">This is an automated email. Please do not reply directly.</p>
        </div>
    </div>
</body>
</html>