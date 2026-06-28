<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice['invoice_number'] ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: #f0f2f5;
            padding: 40px 20px;
            color: #333;
        }

        .invoice-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 50px 60px 30px;
        }

        /* Top Section - Vendor Left, Customer Right */
        .top-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
        }

        .vendor-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .vendor-info .subtitle {
            font-size: 14px;
            color: #888;
            font-weight: 400;
            margin-bottom: 10px;
        }

        .vendor-info .details {
            font-size: 14px;
            color: #555;
            line-height: 1.8;
        }

        .vendor-info .details strong {
            color: #333;
        }

        .customer-info {
            text-align: right;
            padding-top: 4px;
        }

        .customer-info .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .customer-info .name {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .customer-info .details {
            font-size: 14px;
            color: #555;
            line-height: 1.8;
        }

        /* Order Info Section */
        .order-info-section {
            text-align: right;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 35px;
            padding: 20px 0;
            border-bottom: 2px solid #f0f0f0;
        }



        .order-info-item .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .order-info-item .value {
            font-size: 15px;
            font-weight: 500;
            color: #1a1a2e;
        }
        .order-info-item .value .invoice-number {
            font-weight: 700;
            color: #1a1a2e;
        }

        /* Items Table */
        .items-table-wrapper {
            overflow-x: auto;
            margin: 0 0 25px 0;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table thead {
            background: #f8f9fa;
        }

        .items-table thead th {
            padding: 12px 16px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
        }

        .items-table thead th:last-child,
        .items-table tbody td:last-child,
        .items-table tfoot td:last-child {
            text-align: right;
        }

        .items-table thead th:first-child {
            text-align: left;
        }

        .items-table tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
            color: #444;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody .product-name {
            font-weight: 500;
            color: #222;
        }

        .items-table tbody .product-sku {
            font-size: 12px;
            color: #999;
            margin-top: 2px;
        }

        /* Totals Section - Right Aligned */
        .totals-wrapper {
            text-align: right;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            margin-bottom: 25px;
        }

        .totals-table {
            width: 340px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 8px 0;
            font-size: 14px;
            color: #555;
        }

        .totals-table td:last-child {
            text-align: right;
            font-weight: 500;
        }

        .totals-table .subtotal-row td {
            padding-top: 0;
        }

        .totals-table .discount-row td {
            color: #28a745;
        }

        .totals-table .total-row td {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            padding-top: 12px;
            border-top: 2px solid #1a1a2e;
        }

        /* Notes Section */
        .notes-section {
            margin: 20px 0 30px;
            padding: 16px 20px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #1a1a2e;
        }

        .notes-section .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #999;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .notes-section p {
            font-size: 14px;
            color: #555;
            line-height: 1.6;
        }

        /* Footer - Admin Text */
        .invoice-footer {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
            text-align: center;
        }

        .invoice-footer .admin-text {
            font-size: 13px;
            color: #999;
            line-height: 1.8;
        }

        .invoice-footer .admin-text strong {
            color: #666;
            font-weight: 600;
        }

        .invoice-footer .footer-links {
            margin-top: 8px;
        }

        .invoice-footer .footer-links a {
            color: #6c757d;
            text-decoration: none;
            margin: 0 12px;
            font-size: 13px;
        }

        .invoice-footer .footer-links a:hover {
            color: #1a1a2e;
            text-decoration: underline;
        }

        .invoice-footer .thank-you {
            font-size: 14px;
            color: #1a1a2e;
            font-weight: 500;
            margin-bottom: 6px;
        }

        /* Print Styles */
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }

            .invoice-wrapper {
                box-shadow: none !important;
                border-radius: 0 !important;
                padding: 40px 50px !important;
            }

            .items-table thead {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .notes-section {
                background: #f8f9fa !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .invoice-wrapper {
                padding: 25px 20px;
            }

            .top-section {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .customer-info {
                text-align: left;
            }

            .order-info-section {
                grid-template-columns: 1fr 1fr;
                gap: 15px;
            }


        }

        @media (max-width: 480px) {
            .order-info-section {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .items-table tbody td {
                padding: 10px 8px;
                font-size: 13px;
            }

            .items-table thead th {
                padding: 10px 8px;
                font-size: 11px;
            }

            .vendor-info h1 {
                font-size: 22px;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-wrapper">
        <!-- Top Section: Vendor Info (Left) | Customer Info (Right) -->
        <table style="width:100%; border-bottom:2px solid #f0f0f0; margin-bottom:30px; padding-bottom:30px;">
            <tr>
                <td style="width:40%; vertical-align:top;">
                    <!-- Vendor Info -->
                    @if($vendor['business_logo'])
                    @php
                    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                    $data = file_get_contents($logoPath);
                    $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    @endphp
                    <img src="{{ $logo }}" style="width:100px;" alt="Logo">
                    @endif

                    <h1>{{ $vendor['business_name'] }}</h1>

                    <div>
                        {{ $vendor['business_address'] }}<br>
                        {{ $vendor['business_email'] }}<br>
                        {{ $vendor['business_phone'] }}
                    </div>
                </td>
                <td style="text-align: center; width:20%; vertical-align:top;"">
                          <span class=" value invoice-number">{{ $invoice['invoice_number'] ?? 'N/A' }}</span>

                </td>

                <td style="width:40%; text-align:right; vertical-align:top;">
                    <!-- Customer Info -->
                    <div style="font-size:18px; font-weight:bold;">
                        {{ $customer['name'] }}
                    </div>

                    {{ $customer['email'] }}<br>
                    {{ $customer['phone'] }}<br>
                    {{ $billing_address }}
                </td>
            </tr>
        </table>

        <!-- Order Info Section -->
        <div class="order-info-section">

            <div class="order-info-item">
                <span class="label">Invoice Date</span>
                <span class="value">{{ $invoice['date'] }}</span>
            </div>
            <div class="order-info-item">
                <span class="label">Order Status</span>
                <span class="value">
                    {{ ucfirst($order['status'] ?? 'Pending') }}
                </span>
            </div>
            <div class="order-info-item">
                <span class="label">Payment Status</span>
                <span class="value">
                    {{ ucfirst($order['payment_status'] ?? 'Pending') }}
                </span>
            </div>
            <div class="order-info-item">
                <span class="label">Due Date</span>
                <span class="value">{{ $order['due_at'] ?? ''}}</span>
            </div>
        </div>

        <!-- Items Table -->
        <div class="items-table-wrapper">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">Description</th>
                        <th style="width: 15%;">SKU</th>
                        <th style="width: 15%;">Unit Price</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 10%;">Discount</th>
                        <th style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items ?? [] as $item)
                    <tr>
                        <td>
                            <div class="product-name">{{ $item['product_name'] }}</div>
                            <div class="product-sku">{{ $item['product_sku'] }}</div>
                        </td>
                        <td>{{ $item['product_sku'] ?? 'SKU-001' }}</td>
                        <td>${{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                        <td>{{ $item['quantity'] ?? 1 }}</td>
                        <td>${{ number_format($item['discount_amount'] ?? 0, 2) }}</td>
                        <td>${{ number_format($item['total_price'] ?? 0, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px; color: #999;">
                            No items found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Totals Section - Right Aligned -->
        <table style="width:100%; margin-top:20px;">
            <tr>
                <td style="width:60%;"></td>
                <td style="width:40%;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td>Subtotal</td>
                            <td style="text-align:right;">
                                ${{ number_format($order['subtotal'] ?? 0, 2) }}
                            </td>
                        </tr>

                        @if(($order['discount_amount'] ?? 0) > 0)
                        <tr>
                            <td>Discount</td>
                            <td style="text-align:right;">
                                -${{ number_format($order['discount_amount'], 2) }}
                            </td>
                        </tr>
                        @endif

                        @if(($order['tax_amount'] ?? 0) > 0)
                        <tr>
                            <td>Tax</td>
                            <td style="text-align:right;">
                                ${{ number_format($order['tax_amount'], 2) }}
                            </td>
                        </tr>
                        @endif

                        @if(($order['shipping_cost'] ?? 0) > 0)
                        <tr>
                            <td>Shipping Cost</td>
                            <td style="text-align:right;">
                                ${{ number_format($order['shipping_cost'], 2) }}
                            </td>
                        </tr>
                        @endif

                        <tr>
                            <td style="border-top:2px solid #000; padding-top:10px;">
                                <strong>Total</strong>
                            </td>
                            <td style="border-top:2px solid #000; padding-top:10px; text-align:right;">
                                <strong>${{ number_format($order['total_amount'] ?? 0, 2) }}</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <!-- Notes Section -->
        @if(isset($order['notes']) && $order['notes'])
        <div class="notes-section">
            <div class="label">Notes</div>
            <p>{{ $order['notes'] }}</p>
        </div>
        @endif

        <!-- Footer - Admin Text -->
        <div class="invoice-footer">
            <div class="thank-you">Thank you for your business!</div>
            <div class="admin-text">
                <strong>Generated by:</strong> {{ $vendor['name'] ?? $company['name'] ?? 'System' }}
                | <strong>Date:</strong> {{ now()->format('F d, Y h:i A') }}
                | <strong>Invoice #:</strong> {{ $invoice['invoice_number'] ?? 'N/A' }}
            </div>
        </div>

    </div>
</body>

</html>