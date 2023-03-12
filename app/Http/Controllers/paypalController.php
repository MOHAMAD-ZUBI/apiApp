<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use App\Models\paypalCheckouts;
use App\Models\products;
use GuzzleHttp\Client;
use App\Models\User;

class paypalController extends Controller
{
    // important //
    // API INPUTS ARE -> [token] & [product_id] & [product_type] & [payment] could be -> {coin or paypal}

    public function createOrder(Request $request)
    {
        $user = User::where(['token' => $request->token])->first();
        
        $product = products::find($request->product_id);
        $product_id = $request->product_id;
        $product_type = $request->product_type;
        $price = $product->$product_type;
        if($price == 0){
            return response()->json("الباقة غير متوفرة");
        }
        $paymentType = $request->payment;

        if($paymentType == 'paypal'){
           
                $environment = new SandboxEnvironment(env('PAYPAL_CLIENT_ID'), env('PAYPAL_SECRET'));
                $client = new PayPalHttpClient($environment);
        
                
                
            
                $ordersRequest = new OrdersCreateRequest();
                $ordersRequest->prefer('return=representation');
                $ordersRequest->body = [
                    "intent" => "CAPTURE",
                    "purchase_units" => [
                        [
                            "amount" => [
                                "currency_code" => "USD",
                                "value" => $price
                            ]
                        ]
                    ],
                    "application_context" => [
                        "return_url" =>  Route('paymentSucessPaypal', ['paymentId' => '']), // include paymentId as a query parameter
                        "cancel_url" => Route('paymentCancellPaypal', ['paymentId' => '']) // include paymentId as a query parameter
                    ]
                ];
            
                try {
                    
                    $response = $client->execute($ordersRequest);

                    // Get the links from the response
                    $links = $response->result->links;

                    // Return the links as a JSON response
                    
                    // Store the payment details in the database
                    $pay = new paypalCheckouts();
                    $pay->user_id = $user->id; // Assuming you have a logged in user
                    $pay->payment_id = $response->result->id;
                    $pay->status = $response->result->status;
                    $pay->product_id = $request->product_id;
                    $pay->amount = $price . " USD";
                    $pay->TYPE = $request->payment;
                    $pay->product_type = $request->product_type;
                    $pay->save();

                    // update the return_url and cancel_url with the payment ID
                    $returnUrl = str_replace('', $pay->payment_id, $ordersRequest->body['application_context']['return_url']);
                    $cancelUrl = str_replace('', $pay->payment_id, $ordersRequest->body['application_context']['cancel_url']);
                    $ordersRequest->body['application_context']['return_url'] = $returnUrl;
                    $ordersRequest->body['application_context']['cancel_url'] = $cancelUrl;

                    return response()->json($links);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => $e->getMessage()
                    ], 400);
                }
        }elseif ($paymentType == 'coin') {
            # code...
            $client = new Client(['headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-CC-Api-Key' => '812bba65-012b-4e5e-b0fa-685c9a1f53e3',
                'X-CC-Version' => '2018-03-22'
            ]]);
    
            $data = [
                'local_price' => [
                    'currency' => 'USD',
                    'amount' => $price,
                ],
                'pricing_type' => 'fixed_price',
                'description' => $product_type,
                'name' => $product->title,
                'metadata' => [
                    'customer_id' => $user->id,
                    'customer_name' => $user->username,
                ],
            ];
    
            $response = $client->post('https://api.commerce.coinbase.com/charges', [
                'json' => $data
            ]);
             

            $json = $response->getBody()->getContents();
            $data = json_decode($json, true);
            $hostedUrl = $data['data']['hosted_url'];
            $payment = new paypalCheckouts();
                    $payment->user_id =  $user->id;
                    // Assuming you have a logged in user
                    $payment->payment_id = $data['data']['code'];
                    $payment->status = 'NEW';
                    $payment->product_id = $request->product_id;
                    $payment->amount = $price . " USD";
                    $payment->TYPE = $request->payment;
                    $payment->product_type = $request->product_type;
                    $payment->save();
            return response()->json($hostedUrl);
        }else {
            return response()->json("No such payment method!");
        }
       
    }

    public function success(Request $request)
    {
        // Payment successful
        // Retrieve the payment ID from the query parameter and update the payment status
        $paymentID = $request->input('paymentId');
        $payment = paypalCheckouts::where('payment_id', $paymentID)->first();
        $payment->status = 'completed'; // Or whatever status you want to set
        $payment->save();
    }
    
    public function cancel(Request $request)
    {
        // Payment cancelled
        // Retrieve the payment ID from the query parameter and update the payment status
        $paymentID = $request->input('paymentId');
        $payment = paypalCheckouts::where('payment_id', $paymentID)->first();
        $payment->status = 'cancelled'; // Or whatever status
        $payment->save();
    }

   
}
