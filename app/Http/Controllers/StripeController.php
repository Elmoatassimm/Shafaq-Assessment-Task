<?php





















// --------------------------------------------------------------------------------
            // Simulate Payment Intent Creation
            //
            // In our current development and testing environment, the Stripe packages are not
            // working correctly in  Vercel environment with the Laravel backend. To work
            // around this limitation, we simulate the payment process by generating fake values.
            // This allows us to test the payment flow (including storing payment records) without
            // actually interacting with Stripe's live API.
            //
            // If you want to test real Stripe integration, you can use the original code above by
            // removing this simulated code block and uncommenting the original code. Text me to give
            // you a test secret key.
            // --------------------------------------------------------------------------------







namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Http\JsonResponse;
use Stripe\Exception\ApiErrorException;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function createPaymentIntent(): JsonResponse
    {

        
        // Check if the user already completed a successful payment
        
        $existingPayment = Payment::where('user_id', Auth::id())
            ->where('status', 'success')
            ->first();

        if ($existingPayment) {
            return response()->json([
                'message' => 'User already paid'
            ], 400);
        }

        try {





            /*

                      // Set Stripe API key
       
      //   Stripe::setApiKey(env("STRIPE_SECRET_KEY"));

            // Create a payment intent with a fixed amount (e.g., $1.00 in cents)
            $paymentIntent = PaymentIntent::create([
                'amount' => 100,
                'currency' => 'usd',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'user_id' => Auth::id()
                ]
            ]);


            $clientSecret = $paymentIntent->client_secret;
            $paymentIntentId = $paymentIntent->id;
            */



            // Simulate creating a payment intent by generating fake values.
            // Generate a simulated client secret and payment intent ID.
            $paymentIntentId = 'pi_' . bin2hex(random_bytes(8));
$clientSecret = $paymentIntentId . '_secret_' . bin2hex(random_bytes(16));

            // Create and store a new payment record with a "pending" status.
            $payment = Payment::create([
                'user_id' => Auth::id(),
                'payment_intent_id' => $paymentIntentId,

                'status' => 'success'
            ]);


            return response()->json([
                'clientSecret' => $clientSecret,
                'paymentIntentId' => $paymentIntentId
            ]);
        } catch (\Exception $e) {
            // Return a JSON error response if something goes wrong.
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }


/*
    public function handleWebhook(Request $request)
    {
        // Set Stripe API key
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // Retrieve webhook payload
        $payload = @file_get_contents('php://input');
        $event = null;

        try {
            // Construct event from JSON payload
            $event = \Stripe\Event::constructFrom(
                json_decode($payload, true)
            );
        } catch (\UnexpectedValueException $e) {
            // Return error response if payload is invalid
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        // Process different webhook event types
        switch ($event->type) {
            case 'payment_intent.succeeded':
                // Update payment status to 'success' if payment was successful
                $paymentIntent = $event->data->object;
                $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();
                if ($payment) {
                    $payment->update(['status' => 'success']);
                }
                break;

            case 'payment_intent.payment_failed':
                // Update payment status to 'failed' if payment failed
                $paymentIntent = $event->data->object;
                $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();
                if ($payment) {
                    $payment->update(['status' => 'failed']);
                }
                break;
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }

    */
}
