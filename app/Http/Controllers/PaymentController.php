<?php
namespace Acelle\Http\Controllers;
use Illuminate\Http\Request;
use Acelle\Model\Payment;
use Acelle\Library\Log as PaymentLog;
use Illuminate\Support\Facades\Log as LaravelLog;

class PaymentController extends Controller {
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', ['except' => [
            'avatar'
        ]]);
    }

    /**
     * Check billing information exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkBillingInformation(Request $request, $subscription)
    {
        if (null == $request->session()->get('billing_information') && $subscription->isTaxBillingRequired()) {
            return action('PaymentController@billingInformation', $subscription->uid);
        } else {
            return true;
        }
    }

    /**
     * Subscription billing information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function billingInformation(Request $request, $subscription_uid) {
        $customer = $request->user()->customer;
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $billing_information = [];

        // Get information from contact
        $contact = $request->user()->customer->contact;
        if (is_object($contact)) {
            $billing_information['tax_number'] = $contact->tax_number;
            $billing_information['billing_address'] = $contact->billing_address;
        }

        // From session if exist
        if (null !== $request->session()->get('billing_information')) {
            $billing_information = $request->session()->get('billing_information');
        }

        // Get old post values
        if (!empty($request->old())) {
            $billing_information = $request->old();
        }

        $rules = [
            'tax_number' => 'required',
            'billing_address' => 'required',
        ];

        // validate and save billing information to session
        if ($request->isMethod('post')) {
            $this->validate($request, $rules);

            $billing_information = $request->all();
            $request->session()->set('billing_information', $billing_information);

            return redirect()->away($request->session()->get('current_payment_link'));
        }

        return view('payments.billing_information', [
            'subscription' => $subscription,
            'rules' => $rules,
            'billing_information' => $billing_information
        ]);
    }

    /**
     * Subscription pay by PayPal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paypal(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $order_id = $subscription->getOrderID();

        // Check billing information
        if( $this->checkBillingInformation($request, $subscription) !== true ) {
            return redirect()->away($this->checkBillingInformation($request, $subscription));
        }

        $payment_method = \Acelle\Model\PaymentMethod::getByType(\Acelle\Model\PaymentMethod::TYPE_PAYPAL);

        // validate and save posted data
        if ($request->isMethod('post')) {
            try {
                $access_token = $payment_method->getPayPalAccessToken()["token"];
                $result = $payment_method->checkPayPalPaymentSuccess($request->paymentID, $request->payerID, $access_token, $subscription);

                $payment = new Payment();
                $payment->subscription_id = $subscription->id;
                $payment->payment_method_id = $payment_method->id;
                $payment->data = serialize($result);
                $payment->status = $result->success ? \Acelle\Model\Payment::STATUS_SUCCESS : \Acelle\Model\Payment::STATUS_FAILED;
                $payment->action = \Acelle\Model\Payment::ACTION_PAID;
                $payment->payment_method_name = trans('messages.' . $payment_method->type);
                $payment->order_id = $order_id;

                // billing information
                $billing_information = $request->session()->get('billing_information');
                if (isset($billing_information)) {
                    $payment->tax_number = $billing_information['tax_number'];
                    $payment->billing_address = $billing_information['billing_address'];
                    $request->session()->forget('billing_information');
                }

                $payment->save();

                if ($result->success) {
                    $subscription->setPaid();

                    // try enabling the subscription, proceed anyway if failed
                    try {
                        $subscription->enable();
                    } catch (\Exception $ex) {
                        // just suppress the error and leave the subscription disabled
                        LaravelLog::warning("Cannot enable subscription {$subscription->id}, proceed anyway");
                    }

                    return redirect()->action('PaymentController@success', $subscription->uid);
                } else {
                    throw new \Exception($result->error);
                }
            } catch (\Exception $e) {
                PaymentLog::error(trans('messages.something_went_wrong_with_payment') . ': ' .$e->getMessage());
                return view('somethingWentWrong', ['message' => trans('messages.something_went_wrong_with_payment', ['error' => $e->getMessage()])]);
            }
        }

        return view('payments.paypal', [
            'subscription' => $subscription,
            'order_id' => $order_id,
            'payment_method' => $payment_method,
        ]);
    }

    /**
     * Subscription pay by Braintree credit card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function braintree_credit_card(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $result = NULL;

        // Check billing information
        if( $this->checkBillingInformation($request, $subscription) !== true ) {
            return redirect()->away($this->checkBillingInformation($request, $subscription));
        }

        $payment_method = \Acelle\Model\PaymentMethod::getByType(\Acelle\Model\PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD);

        try {
            $clientToken =  $payment_method->getBraintreeClientToken();

            $order_id = $subscription->getOrderID();

            // validate and save posted data
            if ($request->isMethod('post')) {
                $nonceFromTheClient = $request->payment_method_nonce;

                $result = \Braintree_Transaction::sale([
                    'amount' => $subscription->price,
                    'paymentMethodNonce' => $nonceFromTheClient,
                    'merchantAccountId' => $payment_method->getOption('merchantAccountID'),
                    "orderId" => $order_id,
                    'options' => [
                      'submitForSettlement' => True
                    ]
                ]);

                $payment = new Payment();
                $payment->subscription_id = $subscription->id;
                $payment->payment_method_id = $payment_method->id;
                $payment->data = serialize($result);
                $payment->status = $result->success ? \Acelle\Model\Payment::STATUS_SUCCESS : \Acelle\Model\Payment::STATUS_FAILED;
                $payment->action = \Acelle\Model\Payment::ACTION_PAID;
                $payment->payment_method_name = trans('messages.' . $payment_method->type);
                $payment->order_id = $order_id;

                // billing information
                $billing_information = $request->session()->get('billing_information');
                if (isset($billing_information)) {
                    $payment->tax_number = $billing_information['tax_number'];
                    $payment->billing_address = $billing_information['billing_address'];
                    $request->session()->forget('billing_information');
                }

                $payment->save();

                if ($result->success) {
                    $subscription->setPaid();

                    // try enabling the subscription, proceed anyway if failed
                    try {
                        $subscription->enable();
                    } catch (\Exception $ex) {
                        // just suppress the error and leave the subscription disabled
                        LaravelLog::warning("Cannot enable subscription {$subscription->id}, proceed anyway");
                    }

                    return redirect()->action('PaymentController@success', $subscription->uid);
                }
            }

        } catch (\Exception $e) {
            PaymentLog::error(trans('messages.something_went_wrong_with_payment') . ': ' .$e->getMessage());
            return view('somethingWentWrong', ['message' => trans('messages.something_went_wrong_with_payment', ['error' => $e->getMessage()])]);
        }

        return view('payments.braintree_credit_card', [
            'subscription' => $subscription,
            'clientToken' => $clientToken,
            'result' => $result,
            'payment_method' => $payment_method,
        ]);
    }

    /**
     * Subscription pay by Braintree credit card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function braintree_paypal(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $result = null;

        // Check billing information
        if( $this->checkBillingInformation($request, $subscription) !== true ) {
            return redirect()->away($this->checkBillingInformation($request, $subscription));
        }

        $payment_method = \Acelle\Model\PaymentMethod::getByType(\Acelle\Model\PaymentMethod::TYPE_BRAINTREE_PAYPAL);

        try {
            $clientToken =  $payment_method->getBraintreeClientToken();

            $order_id = $subscription->getOrderID();

            // validate and save posted data
            if ($request->isMethod('post')) {
                $nonceFromTheClient = $request->payment_method_nonce;

                $result = \Braintree_Transaction::sale([
                    "amount" => $subscription->price,
                    "paymentMethodNonce" => $nonceFromTheClient,
                    'merchantAccountId' => $payment_method->getOption('merchantAccountID'),
                    "orderId" => $order_id,
                    'options' => [
                      'submitForSettlement' => True
                    ]
                ]);

                $payment = new Payment();
                $payment->subscription_id = $subscription->id;
                $payment->data = serialize($result);
                $payment->payment_method_id = $payment_method->id;
                $payment->status = $result->success ? \Acelle\Model\Payment::STATUS_SUCCESS : \Acelle\Model\Payment::STATUS_FAILED;
                $payment->action = \Acelle\Model\Payment::ACTION_PAID;
                $payment->payment_method_name = trans('messages.' . $payment_method->type);
                $payment->order_id = $order_id;

                // billing information
                $billing_information = $request->session()->get('billing_information');
                if (isset($billing_information)) {
                    $payment->tax_number = $billing_information['tax_number'];
                    $payment->billing_address = $billing_information['billing_address'];
                    $request->session()->forget('billing_information');
                }

                $payment->save();

                if ($result->success) {
                    $subscription->setPaid();

                    // try enabling the subscription, proceed anyway if failed
                    try {
                        $subscription->enable();
                    } catch (\Exception $ex) {
                        // just suppress the error and leave the subscription disabled
                        LaravelLog::warning("Cannot enable subscription {$subscription->id}, proceed anyway");
                    }

                    return redirect()->action('PaymentController@success', $subscription->uid);
                }
            }
        } catch (\Exception $e) {
            PaymentLog::error(trans('messages.something_went_wrong_with_payment') . ': ' .$e->getMessage());
            return view('somethingWentWrong', ['message' => trans('messages.something_went_wrong_with_payment', ['error' => $e->getMessage()])]);
        }

        return view('payments.braintree_paypal', [
            'subscription' => $subscription,
            'clientToken' => $clientToken,
            'result' => $result,
            'payment_method' => $payment_method,
        ]);
    }

    /**
     * Payment success page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function success(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);

        return view('payments.success', [
            'subscription' => $subscription
        ]);
    }

    /**
     * Subscription update paid status from Service.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paymentStatus(Request $request) {
        if($request->tx) {
            if($payment=Payment::where('transaction_id',$request->tx)->first()){
                $payment_id=$payment->id;
            }else{
                $payment=new Payment;
                $payment->item_number = $request->item_number;
                $payment->transaction_id = $request->tx;
                $payment->currency_code = $request->cc;
                $payment->payment_status = $request->st;

                // billing information
                $billing_information = $request->session()->get('billing_information');
                if (isset($billing_information)) {
                    $payment->tax_number = $billing_information['tax_number'];
                    $payment->billing_address = $billing_information['billing_address'];
                    $request->session()->forget('billing_information');
                }

                $payment->save();

                $payment_id=$payment->id;
            }

            return 'Pyament has been done and your payment id is : ' . $payment_id;
        }else{
            return 'Payment has failed';
        }
    }

    /**
     * Subscription pay bay cash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cash(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $payment_method = \Acelle\Model\PaymentMethod::getByType(\Acelle\Model\PaymentMethod::TYPE_CASH);

        // Check billing information
        if( $this->checkBillingInformation($request, $subscription) !== true ) {
            return redirect()->away($this->checkBillingInformation($request, $subscription));
        }

        $payment = new Payment();
        $payment->subscription_id = $subscription->id;
        $payment->payment_method_id = $payment_method->id;
        $payment->status = \Acelle\Model\Payment::STATUS_CASH_MANUAL_CONFIRMATION;
        $payment->action = \Acelle\Model\Payment::ACTION_PAID;;
        $payment->payment_method_name = trans('messages.' . $payment_method->type);
        $payment->order_id = $subscription->getOrderID();

        // billing information
        $billing_information = $request->session()->get('billing_information');
        if (isset($billing_information)) {
            $payment->tax_number = $billing_information['tax_number'];
            $payment->billing_address = $billing_information['billing_address'];
            $request->session()->forget('billing_information');
        }

        $payment->save();

        $request->session()->flash('alert-success', trans('messages.subscription.cash.created'));
        return redirect()->action('AccountController@subscription');
    }

    /**
     * Subscription pay by Stripe credit card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function stripe_credit_card(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $order_id = $subscription->getOrderID();
        $result = null;

        // Check billing information
        if( $this->checkBillingInformation($request, $subscription) !== true ) {
            return redirect()->away($this->checkBillingInformation($request, $subscription));
        }

        $payment_method = \Acelle\Model\PaymentMethod::getByType(\Acelle\Model\PaymentMethod::TYPE_STRIPE_CREDIT_CARD);
        $apiSecretKey = $payment_method->getOption('api_secret_key');
        $apiPublishableKey = $payment_method->getOption('api_publishable_key');

        try {
            \Stripe\Stripe::setApiKey($apiSecretKey);

            // validate and save posted data
            if ($request->isMethod('post')) {

                // Token is created using Stripe.js or Checkout!
                // Get the payment token submitted by the form:
                $token = $request->stripeToken;

                // Charge the user's card:
                $result = \Stripe\Charge::create(array(
                    "amount" => $subscription->stripePrice(),
                    "currency" => $subscription->currency_code,
                    "description" => trans('messages.stripe_checkout_description', ['order' => $order_id]),
                    "source" => $token,
                ));

                $payment = new Payment();
                $payment->subscription_id = $subscription->id;
                $payment->payment_method_id = $payment_method->id;
                $payment->data = serialize($result);
                $payment->status = 'success';
                $payment->action = \Acelle\Model\Payment::ACTION_PAID;
                $payment->payment_method_name = trans('messages.' . $payment_method->type);
                $payment->order_id = $order_id;

                // billing information
                $billing_information = $request->session()->get('billing_information');
                if (isset($billing_information)) {
                    $payment->tax_number = $billing_information['tax_number'];
                    $payment->billing_address = $billing_information['billing_address'];
                    $request->session()->forget('billing_information');
                }

                $payment->save();
                $subscription->setPaid();

                // try enabling the subscription, proceed anyway if failed
                try {
                    $subscription->enable();
                } catch (\Exception $ex) {
                    // just suppress the error and leave the subscription disabled
                    LaravelLog::warning("Cannot enable subscription {$subscription->id}, proceed anyway");
                }

                return redirect()->action('PaymentController@success', $subscription->uid);
            }

        } catch(\Stripe_CardError $e) {
            $error_message = "";
            // Since it's a decline, Stripe_CardError will be caught
            $body = $e->getJsonBody();
            $err  = $body['error'];

            $error_message .= 'Status is:' . $e->getHttpStatus() . "\n";
            $error_message .= 'Type is:' . $err['type'] . "\n";
            $error_message .= 'Code is:' . $err['code'] . "\n";
            // param is '' in this case
            $error_message .= 'Param is:' . $err['param'] . "\n";
            $error_message .= 'Message is:' . $err['message'] . "\n";
        } catch (\Stripe_InvalidRequestError $e) {
            $error_message = $e->getMessage();
        } catch (\Stripe_AuthenticationError $e) {
            // Authentication with Stripe's API failed
            // (maybe you changed API keys recently)
            $error_message = $e->getMessage();
        } catch (\Stripe_ApiConnectionError $e) {
            // Network communication with Stripe failed
        } catch (\Stripe_Error $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            $error_message = $e->getMessage();
        } catch (\Exception $e) {
            // Something else happened, completely unrelated to Stripe
            $error_message = $e->getMessage();
        }

        if (isset($error_message)) {
            PaymentLog::error(trans('messages.something_went_wrong_with_payment') . ': ' .$error_message);
            return view('somethingWentWrong', ['message' => trans('messages.something_went_wrong_with_payment', ['error' => $error_message])]);
        }

        return view('payments.stripe_credit_card', [
            'subscription' => $subscription,
            'apiPublishableKey' => $apiPublishableKey,
            'result' => $result,
            'payment_method' => $payment_method,
        ]);
    }

    /**
     * Subscription pay by Paddle card.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paddle_card(Request $request, $subscription_uid) {
        $subscription = \Acelle\Model\Subscription::findByUid($subscription_uid);
        $orderId = $subscription->getOrderID();
        $result = null;

        // Check billing information
        if( $this->checkBillingInformation($request, $subscription) !== true ) {
            return redirect()->away($this->checkBillingInformation($request, $subscription));
        }

        $payment_method = \Acelle\Model\PaymentMethod::getByType(\Acelle\Model\PaymentMethod::TYPE_PADDLE_CARD);
        $vendorId = $payment_method->getOption('vendor_id');

        // Checkout url
        $data['vendor_id'] = $vendorId;
        $data['vendor_auth_code'] = $payment_method->getOption('vendor_auth_code');

        $data['title'] = trans('messages.paddle_checkout_title', ['plan' => $subscription->plan_name]); //'Plan: ' . $subscription->plan_name ; // name of product
        $data['webhook_url'] = action('PaymentController@paddle_card_hook', $subscription->uid); // URL to call when product is purchased
        $data['quantity_variable'] = 0; // name of product

        // You must provide at least one price for the checkout, here we are setting multiple for different currencies.
        $data['prices'] = [
            $subscription->currency_code . ':0' // . $subscription->price
        ];

        // Setting some other (optional) data.
        $data['custom_message'] = trans('messages.paddle_order_id') . ': ' . $orderId;
        # $data['return_url'] = action('PaymentController@success', $subscription->uid);

        // Here we make the request to the Paddle API
        $url = 'https://vendors.paddle.com/api/2.0/product/generate_pay_link';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        try {
            $response = curl_exec($ch);

            if ($response) {
                // And handle the response...
                $data = json_decode($response);
                if($data->success) {
                    $checkoutUrl = $data->response->url;
                } else {
                    throw new \Exception($data->error->message);
                }
            } else {
                throw new \Exception('Cannot connect to Paddle generate pay link!');
            }
        } catch (\Exception $e) {
            PaymentLog::error(trans('messages.something_went_wrong_with_payment') . ': ' .$e->getMessage());
            return view('somethingWentWrong', ['message' => trans('messages.something_went_wrong_with_payment', ['error' => $e->getMessage()])]);
        }

        return view('payments.paddle_card', [
            'subscription' => $subscription,
            'payment_method' => $payment_method,
            'vendorId' => $vendorId,
            'orderId' => $orderId,
            'checkoutUrl' => $checkoutUrl,
        ]);
    }

    public function paddle_card_hook(Request $request) {
        $arr = $request->all();
        $arr['time'] = \Carbon\Carbon::now();
        \File::put('paddle-process.log', json_encode($arr));
    }
}
