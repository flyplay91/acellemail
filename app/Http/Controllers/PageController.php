<?php

namespace Acelle\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->middleware('auth', [
            'except' => [
                'signUpForm',
                'signUpThankyouPage',
                'signUpConfirmationThankyou',
                'unsubscribeForm',
                'unsubscribeSuccessPage',
                'profileUpdateForm',
                'profileUpdateSuccessPage',
                'profileUpdateEmailSent',
            ],
        ]);
    }

    /**
     * Update list page content.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        
        // authorize
        if (\Gate::denies('update', $list)) {
            return $this->notAuthorized();
        }

        $layout = \Acelle\Model\Layout::where('alias', $request->alias)->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);

        // storing
        if ($request->isMethod('post')) {
            $validate = 'required';
            foreach ($layout->tags() as $tag) {
                if ($tag['required']) {
                    $validate .= '|substring:'.$tag['name'];
                }
            }

            $rules = array(
                'content' => $validate,
            );
            $rules['subject'] = 'required';

            $this->validate($request, $rules);

            // save
            $page->fill($request->all());
            $page->save();

            // Log
            $page->log('updated', $request->user()->customer);

            $request->session()->flash('alert-success', trans('messages.page.updated'));

            return redirect()->action('PageController@update', array('list_uid' => $list->uid, 'alias' => $layout->alias));
        }

        // return back
        $page->fill($request->old());

        return view('pages.update', [
            'list' => $list,
            'page' => $page,
            'layout' => $layout,
        ]);
    }

    /**
     * Preview page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function preview(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);

        // authorize
        if (\Gate::denies('update', $list)) {
            return $this->notAuthorized();
        }

        $layout = \Acelle\Model\Layout::where('alias', $request->alias)->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $page->content = $request->content;

        // render content
        $page->renderContent();

        return view('pages.preview_'.$page->layout->type, [
            'list' => $list,
            'page' => $page,
        ]);
    }

    /**
     * Sign up form page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function signUpForm(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_form')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);

        // Get old post values
        $values = [];
        if (null !== $request->old()) {
            foreach ($request->old() as $key => $value) {
                if (is_array($value)) {
                    $values[str_replace('[]', '', $key)] = implode(',', $value);
                } else {
                    $values[$key] = $value;
                }
            }
        }

        $page->renderContent($values);

        // Create subscriber
        if ($request->isMethod('post')) {
            $subscriber = \Acelle\Model\Subscriber::where("email", "=", strtolower($request->EMAIL))
                ->where("mail_list_id", "=", $list->id)->first();
            if(!is_object($subscriber)) {
                $subscriber = new \Acelle\Model\Subscriber($request->all());
                $subscriber->mail_list_id = $list->id;
            }

            // Validation
            $this->validate($request, $subscriber->getRules());

            $subscriber->email = $request->EMAIL;
            $subscriber->ip = $request->ip();
            $subscriber->from = 'web';
            if($list->subscribe_confirmation) {
                $subscriber->status = 'unconfirmed';
            } else {
                $subscriber->status = 'subscribed';
            }

            $subscriber->save();
            // Update field
            $subscriber->updateFields($request->all());

            if($list->subscribe_confirmation) {
                // SEND subscription confirmation email
                try {
                    $list->sendSubscriptionConfirmationEmail($subscriber);
                } catch (\Exception $ex) {
                    return view('somethingWentWrong', ['message' => $ex->getMessage()]);
                }

                return redirect()->action('PageController@signUpThankyouPage', $list->uid);
            } else {
                // change status to subscribed
                $subscriber->updateStatus('subscribed');

                // Send welcome email
                if($list->send_welcome_email) {
                    // SEND subscription confirmation email
                    try {
                        $list->sendSubscriptionWelcomeEmail($subscriber);
                    } catch (\Exception $ex) {
                        return view('somethingWentWrong', ['message' => $ex->getMessage()]);
                    }
                }

                // find display thank you page
                $layout = \Acelle\Model\Layout::where('alias', 'sign_up_confirmation_thankyou')->first();
                $page = \Acelle\Model\Page::findPage($list, $layout);
                $page->renderContent(null, $subscriber);
            }
        }

        return view('pages.form', [
            'list' => $list,
            'page' => $page,
            'values' => $values,
        ]);
    }

    /**
     * Sign up thank you page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function signUpThankyouPage(Request $request)
    {
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_thankyou_page')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);

        $page->renderContent();

        return view('pages.default', [
            'list' => $list,
            'page' => $page,
        ]);
    }

    /**
     * Sign up confirmation thank you page.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function signUpConfirmationThankyou(Request $request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'sign_up_confirmation_thankyou')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $subscriber = \Acelle\Model\Subscriber::findByUid($request->uid);

        $page->renderContent(null, $subscriber);

        if ($subscriber->getSecurityToken('subscribe-confirm') == $request->code && $subscriber->status == 'unconfirmed') {
            $subscriber->status = 'subscribed';
            $subscriber->save();

            // Send welcome email
            if($list->send_welcome_email) {
                // SEND subscription confirmation email
                try {
                    $list->sendSubscriptionWelcomeEmail($subscriber);
                } catch (\Exception $ex) {
                    return view('somethingWentWrong', ['message' => $ex->getMessage()]);
                }
            }
        }

        return view('pages.default', [
            'list' => $list,
            'page' => $page,
        ]);
    }

    /**
     * Unsibscribe form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function unsubscribeForm(Request $request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'unsubscribe_form')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $subscriber = \Acelle\Model\Subscriber::findByUid($request->uid);

        $page->renderContent(null, $subscriber);

        if ($request->isMethod('post')) {
            if ($subscriber->getSecurityToken('unsubscribe') == $request->code && $subscriber->status == 'subscribed') {
                // Validation
                $this->validate($request, ['EMAIL' => 'required|email|exists:subscribers,email']);

                $subscriber->status = 'unsubscribed';
                $subscriber->save();

                // Send goodbye email
                if($list->unsubscribe_notification) {
                    // SEND subscription confirmation email
                    try {
                        $list->sendUnsubscriptionNotificationEmail($subscriber);
                    } catch (\Exception $ex) {
                        return view('somethingWentWrong', ['message' => $ex->getMessage()]);
                    }
                }
            }
            return redirect()->action('PageController@unsubscribeSuccessPage', ['list_uid' => $list->uid, 'uid' => $subscriber->uid]);
        }

        return view('pages.form', [
            'list' => $list,
            'page' => $page,
        ]);
    }

    /**
     * Unsibscribe form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function unsubscribeSuccessPage(Request $request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'unsubscribe_success_page')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $subscriber = \Acelle\Model\Subscriber::findByUid($request->uid);

        $page->renderContent(null, $subscriber);

        return view('pages.default', [
            'list' => $list,
            'page' => $page,
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Update profile form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function profileUpdateForm(Request $request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'profile_update_form')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $subscriber = \Acelle\Model\Subscriber::findByUid($request->uid);

        $values = [];

        // Fetch subscriber fields to values
        foreach ($list->fields as $key => $field) {
            $value = $subscriber->getValueByField($field);
            if (is_array($value)) {
                $values[str_replace('[]', '', $key)] = implode(',', $value);
            } else {
                $values[$field->tag] = $value;
            }
        }

        // Get old post values
        if (null !== $request->old()) {
            foreach ($request->old() as $key => $value) {
                if (is_array($value)) {
                    $values[str_replace('[]', '', $key)] = implode(',', $value);
                } else {
                    $values[$key] = $value;
                }
            }
        }

        $page->renderContent($values, $subscriber);

        if ($request->isMethod('post')) {
            if ($subscriber->getSecurityToken('update-profile') == $request->code) {
                $rules = $subscriber->getRules();
                $rules['EMAIL'] .= '|in:'.$subscriber->email;
                // Validation
                $this->validate($request, $rules);

                // Update field
                $subscriber->updateFields($request->all());

                return redirect()->action('PageController@profileUpdateSuccessPage', ['list_uid' => $list->uid, 'uid' => $subscriber->uid]);
            }
        }

        return view('pages.form', [
            'list' => $list,
            'page' => $page,
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Update profile success.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function profileUpdateSuccessPage(Request $request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'profile_update_success_page')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $subscriber = \Acelle\Model\Subscriber::findByUid($request->uid);

        $page->renderContent(null, $subscriber);

        return view('pages.default', [
            'list' => $list,
            'page' => $page,
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Send update profile request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function profileUpdateEmailSent(Request $request)
    {
        $user = $request->user();
        $list = \Acelle\Model\MailList::findByUid($request->list_uid);
        $layout = \Acelle\Model\Layout::where('alias', 'profile_update_email_sent')->first();
        $page = \Acelle\Model\Page::findPage($list, $layout);
        $subscriber = \Acelle\Model\Subscriber::findByUid($request->uid);

        $page->renderContent(null, $subscriber);

        // SEND EMAIL
        try {
            $list->sendProfileUpdateEmail($subscriber);
        } catch (\Exception $ex) {
            return view('somethingWentWrong', ['message' => $ex->getMessage()]);
        }

        return view('pages.default', [
            'list' => $list,
            'page' => $page,
            'subscriber' => $subscriber,
        ]);
    }
}
