{{-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stripe Payment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <script src="https://js.stripe.com/v3/"></script>

    <style>
        .StripeElement {
            background-color: white;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .StripeElement--focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Stripe Payment Form</h4>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('stripe.post') }}" method="POST" id="payment-form">
                            @csrf

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (USD)</label>
                                <input type="number" name="amount" class="form-control" id="amount"
                                    placeholder="e.g. 50" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Customer Email</label>
                                <input type="email" class="form-control" id="email"
                                    placeholder="e.g. johndoe@example.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="card-element" class="form-label">Card Details</label>
                                <div id="card-element" class="form-control">

                                </div>
                            </div>

                            <button type="submit" id="submit" class="btn btn-success w-100">Pay Now</button>
                        </form>
                    </div>
                    <div class="card-footer text-muted text-center">
                        Use test card: <strong>4242 4242 4242 4242</strong>
                    </div>
                </div>
                <h5 class="text-center mt-4">OR Pay with Google Pay</h5>
                <div id="google-pay-button" class="d-flex justify-content-center mt-3"></div>
                <script>
                    const stripe = Stripe('{{ env("STRIPE_KEY") }}');
                    const elements = stripe.elements();

                    // Existing Card Element
                    const card = elements.create('card');
                    card.mount('#card-element');

                    // Payment Request API for Google Pay
                    // const paymentRequest = stripe.paymentRequest({
                    //     country: 'PK',  // Pakistan or your country
                    //     currency: 'pkr',
                    //     total: {
                    //         label: 'Total',
                    //         amount: 0,  // We'll update this dynamically on amount input
                    //     },
                    //     requestPayerName: true,
                    //     requestPayerEmail: true,
                    //     // Include Google Pay in allowed payment methods by default
                    // });
const paymentRequest = stripe.paymentRequest({
    country: 'US',
    currency: 'usd',
    total: {
        label: 'Total',
        amount: 0,
    },
    requestPayerName: true,
    requestPayerEmail: true,
});

                    const prButton = elements.create('paymentRequestButton', {
                        paymentRequest: paymentRequest,
                    });

                    // Check availability of Payment Request API
                    paymentRequest.canMakePayment().then(function (result) {
                        if (result) {
                            prButton.mount('#google-pay-button');
                        } else {
                            document.getElementById('google-pay-button').style.display = 'none';
                        }
                    });

                    // Update paymentRequest amount dynamically when user enters amount
                    document.getElementById('amount').addEventListener('input', function (e) {
                        const value = e.target.value;
                        const amountInCents = Math.round(parseFloat(value) * 100);
                        paymentRequest.update({
                            total: {
                                label: 'Total',
                                amount: isNaN(amountInCents) ? 0 : amountInCents,
                            }
                        });
                    });

                    // Handle payment method from Google Pay / Payment Request API
                    paymentRequest.on('paymentmethod', async (ev) => {
                        const amount = Math.round(parseFloat(document.getElementById('amount').value) * 100);
                        if (!amount || amount <= 0) {
                            ev.complete('fail');
                            alert('Please enter a valid amount');
                            return;
                        }

                        try {
                            // Send payment method id and amount to backend to create charge
                            const response = await fetch("{{ route('stripe.post') }}", {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({
                                    payment_method_id: ev.paymentMethod.id,
                                    amount: amount,
                                }),
                            });
                            const data = await response.json();

                            if (data.success) {
                                ev.complete('success');
                                alert('Payment successful!');
                                document.getElementById('payment-form').reset();
                            } else {
                                ev.complete('fail');
                                alert('Payment failed: ' + (data.message || 'Unknown error'));
                            }
                        } catch (error) {
                            ev.complete('fail');
                            alert('Network error: ' + error.message);
                        }
                    });

                </script>

            </div>
        </div>
    </div>
    <script>
        const stripe = Stripe('{{ env("STRIPE_KEY") }}');
        const elements = stripe.elements();
        const card = elements.create('card');
        card.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit');

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            submitButton.disabled = true;
            submitButton.innerText = "Processing...";

            const amount = document.getElementById('amount').value;

            const { token, error } = await stripe.createToken(card);

            if (error) {
                alert("Stripe Error: " + error.message);
                submitButton.disabled = false;
                submitButton.innerText = "Pay Now";
                return;
            }

            console.log("Stripe Token:", token.id);

            // Manually send to Laravel via fetch POST
            fetch("{{ route('stripe.post') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    stripeToken: token.id,
                    amount: amount
                }),
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Payment successful!");
                        form.reset();
                        card.clear();
                    } else {
                        alert("Server Error: " + (data.message || "Something went wrong"));
                    }
                    submitButton.disabled = false;
                    submitButton.innerText = "Pay Now";
                })
                .catch(err => {
                    console.error("Fetch Error:", err);
                    alert("Network Error: " + err.message);
                    submitButton.disabled = false;
                    submitButton.innerText = "Pay Now";
                });
        });
    </script>


</body>
</html> --}}

<!DOCTYPE html>
<html>
<head>
    <title>Stripe Google Pay Test</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<h1>HELLO</h1>
<div id="google-pay-button"></div>

<script>
    const stripe = Stripe('pk_test_51Rm6j32eQaSAHXpWh6dMYUuCTYHXvisXiQxk3MRCIDlWCtKaEd7iWBKK9J70JO0OQTmDzgkMbEHkwpi2WooVLHcx00QEWltHps');

    const paymentRequest = stripe.paymentRequest({
        country: 'US',
        currency: 'usd',
        total: {
            label: 'Test Payment',
            amount: 1000,
        },
        requestPayerName: true,
        requestPayerEmail: true,
    });

    const elements = stripe.elements();
    const prButton = elements.create('paymentRequestButton', {
        paymentRequest,
    });

    paymentRequest.canMakePayment().then(function(result) {
        if (result) {
            prButton.mount('#google-pay-button');
            console.log('Google Pay button mounted');
        } else {
            document.getElementById('google-pay-button').style.display = 'none';
            console.log('Google Pay not available');
        }
    });

    paymentRequest.on('paymentmethod', async (ev) => {
        ev.complete('success');
        alert('Payment method received: ' + ev.paymentMethod.id);
    });
</script>

</body>
</html>
