<?php

/**
 * PaymentMethod class.
 *
 * Model class for payment methods
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   MVC Model
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    // PaymentMethod status
	const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';

	// PaymentMethod type
	const TYPE_CASH = 'cash';
    const TYPE_PAYPAL = 'paypal';
	const TYPE_BRAINTREE_PAYPAL = 'braintree_paypal';
	const TYPE_BRAINTREE_CREDIT_CARD = 'braintree_credit_card';
    const TYPE_STRIPE_CREDIT_CARD = 'stripe_credit_card';
	const TYPE_PADDLE_CARD = 'paddle_card';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'options', 'status'
    ];

    /**
     * The rules for validation.
     *
     * @var array
     */
    public function rules()
    {
        $rules = array(
			'status' => 'required',
        );

        if(\Auth::user()->can('create', $this)) {
			$rules['type'] = 'required';
		}

		if($this->type == PaymentMethod::TYPE_PAYPAL) {
			$rules['options.environment'] = 'required';
			$rules['options.clientID'] = 'required';
			$rules['options.secret'] = 'required';
		}

		if($this->type == PaymentMethod::TYPE_BRAINTREE_PAYPAL || $this->type == PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD) {
			$rules['options.environment'] = 'required';
			$rules['options.merchantId'] = 'required';
			$rules['options.publicKey'] = 'required';
			$rules['options.privateKey'] = 'required';
			$rules['options.merchantAccountID'] = 'required';
		}

        if($this->type == PaymentMethod::TYPE_STRIPE_CREDIT_CARD) {
			$rules['options.api_secret_key'] = 'required';
			$rules['options.api_publishable_key'] = 'required';
		}

		if($this->type == PaymentMethod::TYPE_PADDLE_CARD) {
			$rules['options.vendor_id'] = 'required';
			$rules['options.vendor_auth_code'] = 'required';
		}

		# Check if payment method is valid
        if (!$this->isValid()) {
            $rules['payment_method_not_valid'] = 'required';
		}

        return $rules;
    }

    /**
     * Find item by uid.
     *
     * @return object
     */
    public static function findByUid($uid)
    {
        return self::where('uid', '=', $uid)->first();
    }

    public function admin()
    {
        return $this->belongsTo('Acelle\Model\Admin');
    }

    /**
     * Bootstrap any application services.
     */
    public static function boot()
    {
        parent::boot();

        // Create uid when creating list.
        static::creating(function ($item) {
            // Create new uid
            $uid = uniqid();
            while (PaymentMethod::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;

            // Update custom order
            PaymentMethod::getAll()->increment('custom_order', 1);
            $item->custom_order = 0;
        });
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAll()
    {
        return PaymentMethod::select('*');
    }

    /**
     * Items per page.
     *
     * @var array
     */
    public static $itemsPerPage = 25;

    /**
     * Filter items.
     *
     * @return collect
     */
    public static function filter($request)
    {
        $query = self::select('payment_methods.*');

        // Keyword
        if (!empty(trim($request->keyword))) {
            foreach (explode(' ', trim($request->keyword)) as $keyword) {
                $query = $query->where(function ($q) use ($keyword) {
                    $q->orwhere('payment_methods.name', 'like', '%'.$keyword.'%');
                });
            }
        }

        // filters
        $filters = $request->filters;

		if(!empty($request->admin_id)) {
            $query = $query->where('payment_methods.admin_id', '=', $request->admin_id);
        }

        return $query;
    }

    /**
     * Search items.
     *
     * @return collect
     */
    public static function search($request)
    {
        $query = self::filter($request);

        if (!empty($request->sort_order)) {
            $query = $query->orderBy($request->sort_order, $request->sort_direction);
        } else {
			$query = $query->orderBy("payment_methods.type", "asc");
		}

        return $query;
    }

    /**
     * Disable payment_method.
     *
     * @return boolean
     */
    public function disable()
    {
        $this->status = PaymentMethod::STATUS_INACTIVE;
        return $this->save();
    }

    /**
     * Enable payment_method.
     *
     * @return boolean
     */
    public function enable()
    {
        $this->status = PaymentMethod::STATUS_ACTIVE;
        return $this->save();
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return json_decode($this->options, true);
    }

    /**
     * Get option.
     *
     * @return string
     */
    public function getOption($name)
    {
		$options = $this->getOptions();
        return isset($options[$name]) ? $options[$name] : NULL;
    }

    /**
     * Get customer select2 select options
     *
     * @return array
     */
    public static function select2($request) {
        $data = ['items' => [], 'more' => true];

        $query = \Acelle\Model\PaymentMethod::getAll()->orderBy('custom_order', 'asc');
        if (isset($request->q)) {
            $keyword = $request->q;
            $query = $query->where(function ($q) use ($keyword) {
                $q->orwhere('payment_methods.name', 'like', '%'.$keyword.'%');
            });
        }
        foreach ($query->limit(20)->get() as $payment_method) {
            $data['items'][] = ['id' => $payment_method->uid, 'text' => trans('messages.payment_method_type_' . $payment_method->type)];
        }

        return json_encode($data);
    }

    /**
     * Get all items.
     *
     * @return collect
     */
    public static function getAllActive()
    {
        return PaymentMethod::getAll()
			->where("payment_methods.status", "=", PaymentMethod::STATUS_ACTIVE)
			->orderBy('custom_order', 'asc')->get();
    }

	/**
     * Payment methos types select options.
     *
     * @return array
     */
    public static function typeSelectOptions()
    {
        return [
            ['value' => PaymentMethod::TYPE_CASH, 'text' => trans('messages.' . PaymentMethod::TYPE_CASH)],
			['value' => PaymentMethod::TYPE_BRAINTREE_PAYPAL, 'text' => trans('messages.' . PaymentMethod::TYPE_BRAINTREE_PAYPAL)],
			['value' => PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD, 'text' => trans('messages.' . PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD)],
            ['value' => PaymentMethod::TYPE_STRIPE_CREDIT_CARD, 'text' => trans('messages.' . PaymentMethod::TYPE_STRIPE_CREDIT_CARD)],
        ];
    }

	/**
     * Set Braintree auth info.
     *
     * @return void
     */
    public function getBraintreeClientToken()
    {
        \Braintree_Configuration::environment($this->getOption('environment'));
        \Braintree_Configuration::merchantId($this->getOption('merchantId'));
        \Braintree_Configuration::publicKey($this->getOption('publicKey'));
        \Braintree_Configuration::privateKey($this->getOption('privateKey'));

		return \Braintree_ClientToken::generate();
    }

	/**
     * Set Braintree auth info.
     *
     * @return void
     */
    public function getBraintreeMerchantAccounts()
    {
        \Braintree_Configuration::environment($this->getOption('environment'));
        \Braintree_Configuration::merchantId($this->getOption('merchantId'));
        \Braintree_Configuration::publicKey($this->getOption('publicKey'));
        \Braintree_Configuration::privateKey($this->getOption('privateKey'));

		$gateway = \Braintree_Configuration::gateway();
		$merchantAccountIterator = $gateway->merchantAccount()->all();

		$accounts = [];
		foreach($merchantAccountIterator as $merchantAccount) {
			$accounts[] = $merchantAccount;
		}

		return $accounts;
    }

	/**
     * Set Braintree auth info.
     *
     * @return void
     */
    public function getBraintreeMerchantAccountSelectOptions($accounts)
    {
		$options = [];
		foreach($accounts as $merchantAccount) {
			$options[] = ['value' => $merchantAccount->id, 'text' => $merchantAccount->id.' / '.$merchantAccount->currencyIsoCode.($merchantAccount->default ? ' (' . trans('messages.default').')' : '')];
		}

		return $options;
    }

	/**
     * Get Braintree merchant account by ID.
     *
     * @return void
     */
    public function getBraintreeMerchantAccountByID($accounts, $id)
    {
		$options = [];
		foreach($accounts as $merchantAccount) {
			if ($merchantAccount->id == $id) {
				return $merchantAccount;
			}
		}

		return $accounts[0];
    }

	/**
     * Get payment method by type.
     *
     * @return object
     */
    public static function getByType($type)
    {
        return PaymentMethod::where("payment_methods.type", "=", $type)
			->first();
    }

	/**
     * Check payment method setting is valid.
     *
     * @return boolean
     */
    public function isValid()
    {
        try {
            if($this->type == PaymentMethod::TYPE_BRAINTREE_PAYPAL || $this->type == PaymentMethod::TYPE_BRAINTREE_CREDIT_CARD) {
				$this->getBraintreeClientToken();
			}
		} catch (\Exception $e) {
			return false;
		}

		return true;
    }

	/**
     * Payment methods status select options.
     *
     * @return array
     */
    public static function statusSelectOptions()
    {
        return [
            ['value' => PaymentMethod::STATUS_ACTIVE, 'text' => trans('messages.payment_method_status_' . PaymentMethod::STATUS_ACTIVE)],
			['value' => PaymentMethod::STATUS_INACTIVE, 'text' => trans('messages.payment_method_status_' . PaymentMethod::STATUS_INACTIVE)],
        ];
    }

    /**
     * Get PayPal Api Access token
     *
     * @return array
     */
    function getPayPalAccessToken() {
        $ch = curl_init();

		// Get options from payment method PayPal
        $clientId = $this->getOption('clientID');
        $secret = $this->getOption('secret');

		// Request to PayPal enpoint
        curl_setopt($ch, CURLOPT_URL, "https://api" . ( $this->getOption('environment') == 'sandbox' ? ".sandbox" : '' ). ".paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($ch);

		// Check the result
        if(!empty($result)) {
            $json = json_decode($result);

			// Token success
            if (isset($json->access_token)) {
                return ['success' => true, 'token' => $json->access_token];
			// Check if has any error
            } elseif (isset($json->error)) {
				throw new \Exception($json->error . ': ' . $json->error_description);
			}
        }

        throw new \Exception(trans("messages.paypal_error_cannot_get_access_token"));
    }

    /**
     * Check PayPal Payment success
     *
     * @return array
     */
    function checkPayPalPaymentSuccess($paymentID, $payerID, $access_token, $subscription) {
        $ch = curl_init();

		// Request to PayPal enpoint
        curl_setopt($ch, CURLOPT_URL, "https://api" . ( $this->getOption('environment') == 'sandbox' ? ".sandbox" : '' ). ".paypal.com/v1/payments/payment/$paymentID");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type:application/json',
            'Authorization: Bearer ' . $access_token
        ]);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

		// Check the result
        if(!empty($result)) {
			$json = json_decode($result);

			// Check if payment state is not approved
			if (!isset($json->state) || $json->state != 'approved') {
				return (object) ['success' => false, 'data' => $json, 'error' => trans("messages.paypal_error_payment_not_approved")];
			}

			// Check if payment amount is not equal to the subscription amount
            if((float) $json->transactions[0]->amount->details->subtotal != (float) $subscription->price) {
                return (object) ['success' => false, 'data' => $json, 'error' => trans("messages.paypal_error_amount_not_equal")];
            }

			// Return success if nothing wrong
			return (object) ['success' => true, 'data' => $result];
        }

		// Uncatchable error
        throw new \Exception(trans("messages.paypal_error_unknown"));
    }
}
