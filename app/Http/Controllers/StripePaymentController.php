<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
class StripePaymentController extends Controller
{
    public function showForm()
    {
        return view('test_stripe', [
            'stripeKey' => env('STRIPE_KEY')
        ]);

    }

    public function makePayment(Request $request)
    {
         
        $newKey="sk_test_51Rm6j32eQaSAHXpWrDsuArvNH4gbI5u1KAyiocY1VgUtCPBGTkVdmyUqFejLSBMaziCerTRYmljMyuJxUrODKiTI00DJkB6wH8";
        Stripe::setApiKey($newKey);

        $amount = $request->amount * 100;  // amount in cents

        try {
            if ($request->has('payment_method_id')) {
                // Payment Request API flow (Google Pay)
                // Create a PaymentIntent with the payment method ID
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amount,
                    'currency' => 'pkr',
                    'payment_method' => $request->payment_method_id,
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'description' => 'New Order Placed via Google Pay',
                ]);

                // Handle next actions (3D Secure, etc.) if required
                if ($paymentIntent->status == 'requires_action' && $paymentIntent->next_action->type == 'use_stripe_sdk') {
                    return response()->json([
                        'requires_action' => true,
                        'payment_intent_client_secret' => $paymentIntent->client_secret,
                    ]);
                } elseif ($paymentIntent->status == 'succeeded') {
                    return response()->json(['success' => true, 'paymentIntent' => $paymentIntent]);
                } else {
                    return response()->json(['success' => false, 'message' => 'Invalid PaymentIntent status']);
                }
            } elseif ($request->has('stripeToken')) {
                // Old Card token flow
                $charge = Charge::create([
                    'amount' => $amount,
                    'currency' => 'pkr',
                    'source' => $request->stripeToken,
                    'description' => 'New Order Placed',
                ]);

                return response()->json(['success' => true, 'charge' => $charge]);
            } else {
                return response()->json(['success' => false, 'message' => 'No payment information provided'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}

