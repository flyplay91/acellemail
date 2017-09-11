<?php

namespace Acelle\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Acelle\Http\Requests;
use Acelle\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // authorize
        if (\Gate::denies('read', new \Acelle\Model\PaymentMethod())) {
            return $this->notAuthorized();
        }

        // If admin can view all sending domains
        if (!$request->user()->admin->can("readAll", new \Acelle\Model\PaymentMethod())) {
            $request->merge(array("admin_id" => $request->user()->admin->id));
        }

        $payment_methods = \Acelle\Model\PaymentMethod::search($request);

        return view('admin.payment_methods.index', [
            'payment_methods' => $payment_methods,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listing(Request $request)
    {
        // authorize
        if (\Gate::denies('read', new \Acelle\Model\PaymentMethod())) {
            return $this->notAuthorized();
        }

        // If admin can view all sending domains
        if (!$request->user()->admin->can("readAll", new \Acelle\Model\PaymentMethod())) {
            $request->merge(array("admin_id" => $request->user()->admin->id));
        }

        $payment_methods = \Acelle\Model\PaymentMethod::search($request)->paginate($request->per_page);

        return view('admin.payment_methods._list', [
            'payment_methods' => $payment_methods,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $payment_method = new \Acelle\Model\PaymentMethod();
        $payment_method->type = \Acelle\Model\PaymentMethod::TYPE_STRIPE_CREDIT_CARD;

        // authorize
        if (\Gate::denies('create', $payment_method)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $payment_method->fill($request->old());
        }

        // For options
        if (isset($request->old()['options'])) {
            $payment_method->options = json_encode($request->old()['options']);
        }
        $options = $payment_method->getOptions();

        return view('admin.payment_methods.create', [
            'payment_method' => $payment_method,
            'options' => $options
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Get current user
        $user = $request->user();
        $payment_method = new \Acelle\Model\PaymentMethod();

        // authorize
        if (\Gate::denies('create', $payment_method)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('post')) {
            $payment_method->fill($request->all());
            $payment_method->options = json_encode($request->options);
            $payment_method->admin_id = $user->admin->id;
            $payment_method->status = \Acelle\Model\PaymentMethod::STATUS_ACTIVE;

            $this->validate($request, $payment_method->rules());

            $payment_method->save();

            $request->session()->flash('alert-success', trans('messages.payment_method.created'));
            return redirect()->action('Admin\PaymentMethodController@index');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $payment_method = \Acelle\Model\PaymentMethod::findByUid($id);

        // authorize
        if (\Gate::denies('update', $payment_method)) {
            return $this->notAuthorized();
        }

        if (!empty($request->old())) {
            $payment_method->fill($request->old());
        }

        // For options
        if (isset($request->old()['options'])) {
            $payment_method->options = json_encode($request->old()['options']);
        }
        $options = $payment_method->getOptions();

        return view('admin.payment_methods.edit', [
            'payment_method' => $payment_method,
            'options' => $options
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Get current user
        $user = $request->user();
        $payment_method = \Acelle\Model\PaymentMethod::findByUid($id);

        // authorize
        if (\Gate::denies('update', $payment_method)) {
            return $this->notAuthorized();
        }

        // save posted data
        if ($request->isMethod('patch')) {
            $payment_method->fill($request->all());
            $payment_method->options = json_encode($request->options);

            $this->validate($request, $payment_method->rules());

            $payment_method->save();

            $request->session()->flash('alert-success', trans('messages.payment_method.updated'));
            return redirect()->action('Admin\PaymentMethodController@edit', $payment_method->uid);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    /**
     * Enable item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Request $request)
    {
        $items = \Acelle\Model\PaymentMethod::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::allows('update', $item)) {
                if (!$item->isValid()) {
                    echo '<span class="is-error">' . trans('messages.payment_methods.not_valid', ['link' => action('Admin\PaymentMethodController@edit', $item->uid)]) . '</span>';
                    return;
                }
                $item->enable();
            }
        }

        // Redirect to my lists page
        echo trans('messages.payment_methods.enabled');
    }

    /**
     * Disable item.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        $items = \Acelle\Model\PaymentMethod::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::allows('update', $item)) {
                $item->disable();
            }
        }

        // Redirect to my lists page
        echo trans('messages.payment_methods.disabled');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $items = \Acelle\Model\PaymentMethod::whereIn('uid', explode(',', $request->uids));

        foreach ($items->get() as $item) {
            // authorize
            if (\Gate::denies('delete', $item)) {
                return;
            }
        }

        foreach ($items->get() as $item) {
            $item->delete();
        }

        // Redirect to my lists page
        echo trans('messages.payment_methods.deleted');
    }

    /**
     * Custom sort items.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sort(Request $request)
    {
        $sort = json_decode($request->sort);
        foreach ($sort as $row) {
            $item = \Acelle\Model\PaymentMethod::findByUid($row[0]);

            // authorize
            if (\Gate::denies('update', $item)) {
                return $this->notAuthorized();
            }

            $item->custom_order = $row[1];
            $item->save();
        }

        echo trans('messages.payment_methods.custom_order.updated');
    }

    /**
     * Select2 payment_method.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function select2(Request $request)
    {
        echo \Acelle\Model\PaymentMethod::select2($request);
    }

    /**
     * Payment method display options form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function options(Request $request, $uid=null)
    {
        if ($uid) {
            $payment_method = \Acelle\Model\PaymentMethod::findByUid($uid);
        } else {
            $payment_method = new \Acelle\Model\PaymentMethod($request->all());
            $payment_method->options = json_encode($request->options);
        }

        return view('admin.payment_methods._options', [
            'payment_method' => $payment_method,
        ]);
    }

     /**
     * Braintree merchant account select form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function braintreeMerchantAccountSelect(Request $request, $uid=null) {
        if ($uid) {
            $payment_method = \Acelle\Model\PaymentMethod::findByUid($uid);
        } else {
            $payment_method = new \Acelle\Model\PaymentMethod();
        }

        $options = $request->options;
        if (!isset($options['merchantAccountID'])) {
            $options['merchantAccountID'] = $payment_method->getOption('merchantAccountID');
        }

        $payment_method->fill($request->all());
        $payment_method->options = json_encode($options);

        $error = false;
        $accounts = NULL;
        try {
            $accounts = $payment_method->getBraintreeMerchantAccounts();
        } catch (\Exception $ex) {
            $error = trans('messages.payment_methods.can_not_get_merchant_accounts');
        }

        return view('admin.payment_methods._braintree_merchant_accounts_select', [
            'payment_method' => $payment_method,
            'accounts' => $accounts,
            'error' => $error,
        ]);

    }
}
