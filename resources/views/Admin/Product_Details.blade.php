<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #{{ $order->order_id }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container-fluid {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Actions */
        .header-actions {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .page-title {
            margin: 0;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-custom {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary-custom {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary-custom:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-secondary-custom {
            background: #f1f5f9;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary-custom:hover {
            background: #e2e8f0;
            text-decoration: none;
            color: var(--text-primary);
        }

        /* Order Tracking */
        .tracking-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 25px;
        }

        .tracking-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .tracking-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 5px;
        }

        .tracking-subtitle {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .progress-container {
            position: relative;
            margin: 30px 0;
        }

        .progress-bar-custom {
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            position: relative;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
            border-radius: 2px;
            transition: width 0.8s ease;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            position: relative;
        }

        .progress-step {
            text-align: center;
            flex: 1;
            position: relative;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .step-circle.completed {
            background: var(--success-color);
            color: white;
        }

        .step-circle.active {
            background: var(--primary-color);
            color: white;
        }

        .step-circle.pending {
            background: var(--border-color);
            color: var(--text-secondary);
        }

        .step-title {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .step-date {
            font-size: 0.8rem;
            color: var(--text-secondary);
        }

        /* Main Content Grid */
        .main-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 25px;
        }

        /* Order Details Card */
        .order-details-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-primary);
        }

        .card-body {
            padding: 20px;
        }

        /* Products Table */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .products-table th {
            padding: 12px;
            text-align: left;
            background: #f8fafc;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .products-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }

        .products-table tr:last-child td {
            border-bottom: none;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            background: #f1f5f9;
        }

        .product-name {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 2px;
        }

        .product-description {
            font-size: 0.85rem;
            color: var(--text-secondary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .price-cell {
            font-weight: 500;
            color: var(--text-primary);
        }

        .quantity-badge {
            background: #f1f5f9;
            color: var(--text-primary);
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.85rem;
        }

        /* Sidebar */
        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .info-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .info-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
            text-align: right;
            max-width: 60%;
        }

        .total-item {
            padding: 12px 0;
            font-size: 1rem;
        }

        .total-item.final {
            border-top: 2px solid var(--border-color);
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--primary-color);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-dispatched {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        /* Receipt Styles */
        .receipt-container {
            display: none;
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            font-family: 'Courier New', monospace;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .receipt-subtitle {
            font-size: 14px;
            color: #666;
        }

        .receipt-info {
            margin-bottom: 20px;
        }

        .receipt-info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .receipt-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .receipt-table th,
        .receipt-table td {
            padding: 8px 4px;
            text-align: left;
            border-bottom: 1px dashed #ccc;
        }

        .receipt-table th {
            font-weight: bold;
            border-bottom: 1px solid #000;
        }

        .receipt-total {
            text-align: right;
            margin-top: 15px;
            font-size: 14px;
        }

        .receipt-total .final-total {
            border-top: 1px solid #000;
            padding-top: 8px;
            font-weight: bold;
            font-size: 16px;
        }

        .receipt-footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }

        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }

            .receipt-container,
            .receipt-container * {
                visibility: visible;
            }

            .receipt-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                display: block !important;
                margin: 0;
                padding: 20px;
                box-shadow: none;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container-fluid {
                padding: 15px;
            }

            .main-content {
                grid-template-columns: 1fr;
            }

            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .action-buttons {
                justify-content: center;
            }

            .progress-steps {
                flex-direction: column;
                gap: 20px;
                text-align: left;
            }

            .progress-step {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .step-circle {
                margin: 0;
            }

            .products-table {
                font-size: 0.85rem;
            }

            .product-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .product-image {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            .products-table th,
            .products-table td {
                padding: 8px 6px;
            }

            .info-item {
                flex-direction: column;
                gap: 5px;
            }

            .info-value {
                max-width: 100%;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <!-- Header Actions -->
    <div class="container-fluid">
        <div class="header-actions">
            <h1 class="page-title">Order #{{ $order->order_id }}</h1>
            <div class="action-buttons">
                <button class="btn-custom btn-primary-custom" id="printBtn">
                    <i class="fas fa-print"></i>
                    Print Receipt
                </button>
                <a href="/" class="btn-custom btn-secondary-custom">
                    <i class="fas fa-arrow-left"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Order Tracking -->
        <div class="tracking-container">
            <div class="tracking-header">
                <h2 class="tracking-title">Order Tracking</h2>
                <p class="tracking-subtitle">Track your order status and delivery progress</p>
            </div>

            <div class="progress-container">
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: {{ $progressWidth }}%;"></div>
                </div>
                <div class="progress-steps">
                    <div class="progress-step">
                        <div class="step-circle {{ $order->status === 'Pending' ? 'active' : ($progressWidth > 0 ? 'completed' : 'pending') }}">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="step-title">Order Placed</div>
                        <div class="step-date">{{ $order->order_datetime->format('M j, Y') }}</div>
                    </div>
                    <div class="progress-step">
                        <div class="step-circle {{ $order->status === 'Dispatched' ? 'active' : ($progressWidth > 50 ? 'completed' : 'pending') }}">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="step-title">Dispatched</div>
                        <div class="step-date">
                            @if($order->status === 'Dispatched' || $order->status === 'Completed')
                                {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M j, Y') : 'Processing' }}
                            @else
                                Pending
                            @endif
                        </div>
                    </div>
                    <div class="progress-step">
                        <div class="step-circle {{ $order->status === 'Completed' ? 'completed' : 'pending' }}">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="step-title">Delivered</div>
                        <div class="step-date">
                            @if($order->status === 'Completed')
                                {{ $order->actual_delivery_date ? $order->actual_delivery_date->format('M j, Y') : 'Delivered' }}
                            @else
                                {{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M j, Y') : 'Pending' }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Order Details -->
            <div class="order-details-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-box"></i>
                        Order Items
                    </h3>
                </div>
                <div class="card-body">
                    <table class="products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->products ?? [] as $product)
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <img src="{{ $product['image'] }}" alt="{{ $product['product_name'] }}" class="product-image">
                                            <div>
                                                <div class="product-name">{{ $product['product_name'] }}</div>
                                                @if($product['description'])
                                                    <div class="product-description">{{ $product['description'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="price-cell">Rs {{ number_format($product['price_at_purchase'], 2) }}</td>
                                    <td>
                                        <span class="quantity-badge">{{ $product['quantity'] }}</span>
                                    </td>
                                    <td class="price-cell" style="text-align: right;">
                                        Rs {{ number_format($product['price_at_purchase'] * $product['quantity'], 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Customer Information -->
                <div class="info-card">
                    <h3 class="info-title">
                        <i class="fas fa-user"></i>
                        Customer Details
                    </h3>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $order->customer->name ?? 'N/A' }}</span>
                    </div>
                    @if($order->customer->phone_no)
                        <div class="info-item">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $order->customer->phone_no }}</span>
                        </div>
                    @endif
                    <div class="info-item">
                        <span class="info-label">Delivery Address:</span>
                        <span class="info-value">{{ $order->delivery_address }}</span>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="info-card">
                    <h3 class="info-title">
                        <i class="fas fa-receipt"></i>
                        Order Summary
                    </h3>
                    <div class="info-item">
                        <span class="info-label">Order Date:</span>
                        <span class="info-value">{{ $order->order_datetime->format('M j, Y h:i A') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ strtolower($order->status) }}">
                                {{ $order->status }}
                            </span>
                        </span>
                    </div>
                    @if($order->expected_delivery_date)
                        <div class="info-item">
                            <span class="info-label">Expected Delivery:</span>
                            <span class="info-value">{{ $order->expected_delivery_date->format('M j, Y') }}</span>
                        </div>
                    @endif
                    @if($order->actual_delivery_date)
                        <div class="info-item">
                            <span class="info-label">Delivered On:</span>
                            <span class="info-value">{{ $order->actual_delivery_date->format('M j, Y') }}</span>
                        </div>
                    @endif
                </div>

                <!-- Payment Summary -->
                <div class="info-card">
                    <h3 class="info-title">
                        <i class="fas fa-credit-card"></i>
                        Payment Details
                    </h3>
                    <div class="info-item">
                        <span class="info-label">Subtotal:</span>
                        <span class="info-value">Rs {{ number_format($order->total_payment, 2) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Shipping:</span>
                        <span class="info-value">Rs 0.00</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tax:</span>
                        <span class="info-value">Rs 0.00</span>
                    </div>
                    <div class="info-item total-item final">
                        <span class="info-label">Total:</span>
                        <span class="info-value">Rs {{ number_format($order->total_payment, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Template (Hidden) -->
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-title">ORDER RECEIPT</div>
            <div class="receipt-subtitle">Thank you for your purchase</div>
        </div>

        <div class="receipt-info">
            <p><strong>Order #:</strong> {{ $order->order_id }}</p>
            <p><strong>Date:</strong> {{ $order->order_datetime->format('M j, Y h:i A') }}</p>
            <p><strong>Customer:</strong> {{ $order->customer->name ?? 'Customer' }}</p>
            <p><strong>Phone:</strong> {{ $order->customer->phone_no ?? 'N/A' }}</p>
            <p><strong>Delivery Address:</strong> {{ $order->delivery_address }}</p>
            <p><strong>Status:</strong> {{ $order->status }}</p>
        </div>

        <table class="receipt-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align: right;">Price</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->products ?? [] as $product)
                    <tr>
                        <td>{{ $product['product_name'] }}</td>
                        <td style="text-align: right;">Rs {{ number_format($product['price_at_purchase'], 2) }}</td>
                        <td style="text-align: center;">{{ $product['quantity'] }}</td>
                        <td style="text-align: right;">Rs {{ number_format($product['price_at_purchase'] * $product['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="receipt-total">
            <p>Subtotal: Rs {{ number_format($order->total_payment, 2) }}</p>
            <p>Shipping: Rs 0.00</p>
            <p>Tax: Rs 0.00</p>
            <p class="final-total">Total: Rs {{ number_format($order->total_payment, 2) }}</p>
        </div>

        @if($order->status === 'Completed' && $order->actual_delivery_date)
            <div style="text-align: center; margin-top: 20px; font-size: 14px;">
                <p><strong>Delivered On:</strong> {{ $order->actual_delivery_date->format('M j, Y') }}</p>
            </div>
        @endif

        <div class="receipt-footer">
            <p>Thank you for your business!</p>
            <p>For inquiries, contact our customer service</p>
            <p>{{ date('Y-m-d H:i:s') }}</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Print functionality
            const printBtn = document.getElementById('printBtn');
            if (printBtn) {
                printBtn.addEventListener('click', function () {
                    const receipt = document.querySelector('.receipt-container');
                    receipt.style.display = 'block';
                    setTimeout(() => {
                        window.print();
                        receipt.style.display = 'none';
                    }, 100);
                });
            }

            // Add smooth animations
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            // Observe all cards for animation
            document.querySelectorAll('.info-card, .order-details-card, .tracking-container').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</body>

</html>