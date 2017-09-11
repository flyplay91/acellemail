<?php

/**
 * Page class.
 *
 * Model class for Page
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

class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 'subject'
    ];

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
            while (Page::where('uid', '=', $uid)->count() > 0) {
                $uid = uniqid();
            }
            $item->uid = $uid;
        });
    }

    /**
     * Associations.
     *
     * @var object | collect
     */
    public function mailList()
    {
        return $this->belongsTo('Acelle\Model\MailList');
    }

    public function layout()
    {
        return $this->belongsTo('Acelle\Model\Layout');
    }

    /**
     * Find a page belong to list with layout.
     *
     * @param List   $list
     * @param string $layout
     *
     * @var Page
     */
    public static function findPage($list, $layout)
    {
        $page = $list->page($layout);
        if (!isset($page)) {
            $page = new \Acelle\Model\Page();
            $page->layout_id = $layout->id;
            $page->mail_list_id = $list->id;
            $page->content = $layout->content;
            $page->subject = $layout->subject;
        }

        return $page;
    }

    /**
     * Render content.
     *
     * @var html
     */
    public function renderContent($values = null, $subscriber = null)
    {
        // BAISC INFO
        $this->content = str_replace('{LIST_NAME}', $this->mailList->name, $this->content);
        $this->content = str_replace('{CONTACT_NAME}', $this->mailList->contact->company, $this->content);
        $this->content = str_replace('{CONTACT_STATE}', $this->mailList->contact->state, $this->content);
        $this->content = str_replace('{CONTACT_ADDRESS_1}', $this->mailList->contact->address_1, $this->content);
        $this->content = str_replace('{CONTACT_ADDRESS_2}', $this->mailList->contact->address_2, $this->content);
        $this->content = str_replace('{CONTACT_CITY}', $this->mailList->contact->city, $this->content);
        $this->content = str_replace('{CONTACT_ZIP}', $this->mailList->contact->zip, $this->content);
        $this->content = str_replace('{CONTACT_COUNTRY}', $this->mailList->contact->country->name, $this->content);
        $this->content = str_replace('{CONTACT_PHONE}', $this->mailList->contact->phone, $this->content);
        $this->content = str_replace('{CONTACT_EMAIL}', $this->mailList->contact->email, $this->content);
        $this->content = str_replace('{CONTACT_URL}', $this->mailList->contact->url, $this->content);

        // FIELDS
        $fields = view('subscribers._form', array('list' => $this->mailList, 'is_page' => true, 'col' => '12', 'values' => $values, 'email_readonly' => true))->render();
        $this->content = str_replace('{FIELDS}', $fields, $this->content);
        $this->content = str_replace('{SUBSCRIBE_BUTTON}', '<button class="btn btn-info bg-teal-800" type="submit">'.trans('messages.subscribe').' <i class="icon-arrow-right14 position-right"></i></button>', $this->content);
        $this->content = str_replace('{EMAIL_FIELD}', view('helpers.form_control', ['type' => 'text', 'name' => 'EMAIL', 'label' => trans('messages.email_address'), 'value' => '', 'rules' => $this->mailList->getFieldRules()])->render(), $this->content);
        $this->content = str_replace('{UNSUBSCRIBE_BUTTON}', '<button class="btn btn-info bg-teal-800" type="submit">'.trans('messages.unsubscribe').' <i class="icon-arrow-right14 position-right"></i></button>', $this->content);
        $this->content = str_replace('{UPDATE_PROFILE_BUTTON}', '<button class="btn btn-info bg-teal-800" type="submit">'.trans('messages.update_profile').' <i class="icon-arrow-right14 position-right"></i></button>', $this->content);
        $this->content = str_replace('{SUBSCRIBE_URL}', action('PageController@signUpForm', $this->mailList->uid), $this->content);

        // SUBSCRIBE CONFIRM URL
        if (isset($subscriber)) {
            // [SUBSCRIBE_CONFIRM_URL]
            $this->content = str_replace('{SUBSCRIBE_CONFIRM_URL}', action('PageController@signUpConfirmationThankyou', array('list_uid' => $this->mailList->uid, 'uid' => $subscriber->uid, 'code' => $subscriber->getSecurityToken('subscribe-confirm'))), $this->content);

            // Summary
            $summary = view('subscribers._summary', ['list' => $this->mailList, 'subscriber' => $subscriber])->render();
            $this->content = str_replace('{SUBSCRIBER_SUMMARY}', $summary, $this->content);

            // UBSUBSCRIBE URL
            $this->content = str_replace('{UNSUBSCRIBE_URL}', action('PageController@unsubscribeForm', array('list_uid' => $this->mailList->uid, 'uid' => $subscriber->uid, 'code' => $subscriber->getSecurityToken('unsubscribe'))), $this->content);

            $this->content = str_replace('{UPDATE_PROFILE_URL}', action('PageController@profileUpdateForm', array('list_uid' => $this->mailList->uid, 'uid' => $subscriber->uid, 'code' => $subscriber->getSecurityToken('update-profile'))), $this->content);
        } else {
            $this->content = str_replace('{SUBSCRIBE_CONFIRM_URL}', "<a href='http://domain.example/secure_token'>http://domain.example/secure_token</a>", $this->content);
            $this->content = str_replace('{UPDATE_PROFILE_URL}', 'http://domain.example/secure_token', $this->content);
            $this->content = str_replace('{UNSUBSCRIBE_URL}', 'http://domain.example/secure_token', $this->content);
            $this->content = str_replace('{SUBSCRIBER_SUMMARY}', '<p><strong>'.trans('messages.email').':</strong></p><p><strong>'.trans('messages.first_name').':</strong></p><p><strong>'.trans('messages.last_name').':</strong></p>', $this->content);
        }

        //// SUBMIT_BUTTON
        //$this->content = str_replace('[SUBMIT_BUTTON]', '<button class="btn btn-primary" type="submit">Submit <i class="icon-arrow-right14 position-right"></i></button>', $this->content);

        //// LIST FIELDS
        //$list_fields = array();
        //foreach($this->mailList->list_fields()->where("visibility", "visible")->get() as $lf) {
        //    $row = array();
        //    $row["item"] = $lf;
        //    $row["value"] = $lf->default_value;
        //    if(isset($subsriber) && $lf->tag == \App\ListField::$main_tag) {
        //        $row["value"] = $subsriber->email;
        //    }
        //    if(isset($request->old()[$lf->tag])) {
        //        $row["value"] = $request->old()[$lf->tag];
        //    }
        //    if(isset($request->all()[$lf->tag])) {
        //        $row["value"] = $request->all()[$lf->tag];
        //    }
        //    $list_fields[] = $row;
        //}
        //$fields = view('subscribers._custom_fields', array("list_fields" => $list_fields))->render();
        //$this->content = str_replace('[LIST_FIELDS]', $fields, $this->content);

        //// [UPDATE_PROFILE_URL]

        //// [UNSUBSCRIBE_EMAIL_FIELD]
        //$email_field = '<div class="row"><div class="col-md-12"><label>Email <span class="text-danger">*</span></label><input name="EMAIL" value="" type="text" class="form-control required" placeholder="Email"></div></div>';
        //$this->content = str_replace('[UNSUBSCRIBE_EMAIL_FIELD]', $email_field, $this->content);

        //if(isset($subsriber)) {
        //    // [UNSUBSCRIBE_CONFIRM_URL]
        //    $this->content = str_replace('[UNSUBSCRIBE_CONFIRM_URL]', action('ListPageController@unsubscribe_confirm', array("list_id" => $this->mailList->list_id, "email" => $subsriber->email, "code" => $subsriber->get_code("unsubscribe-confirm"))), $this->content);

        //    // [SUBSCRIBE_CONFIRM_URL]
        //    $this->content = str_replace('[SUBSCRIBE_CONFIRM_URL]', action('ListPageController@subscribe_confirm', array("list_id" => $this->mailList->list_id, "email" => $subsriber->email, "code" => $subsriber->get_code("subscribe-confirm"))), $this->content);

        //    // [UPDATE_PROFILE_URL]
        //    $this->content = str_replace('[UPDATE_PROFILE_URL]', action('ListPageController@update_profile', array("list_id" => $this->mailList->list_id, "email" => $subsriber->email, "code" => $subsriber->get_code("update-profile"))), $this->content);
        //}

        //// [SUBSCRIBE_URL]
        //$this->content = str_replace('[SUBSCRIBE_URL]', action('ListPageController@subscribe', array("list_id" => $this->mailList->list_id)), $this->content);

        //// [COMPANY_NAME]
        //$this->content = str_replace('[COMPANY_NAME]', $this->mailList->company->name, $this->content);

        //// [CURRENT_YEAR]
        //$this->content = str_replace('[CURRENT_YEAR]', date("Y"), $this->content);
    }

    /**
     * Send list page with email page type.
     *
     * @param Subscriber $subscriber
     * @param string     $title
     *
     * @var void
     */
    public function sendMail($subscriber, $title)
    {
        $page = $this;
        \Mail::send('pages._email_content', ['page' => $page], function ($m) use ($subscriber, $title) {
            $m->from($subscriber->mailList->from_email, $subscriber->mailList->from_name);

            $m->to($subscriber->email, trans('messages.to_email_name'))->subject($title);
        });
    }

    /**
     * Add customer action log.
     */
    public function log($name, $customer, $add_datas = [])
    {
        $data = [
            'id' => $this->id,
            'alias' => $this->layout->alias,
            'list_id' => $this->mail_list_id,
            'list_name' => $this->mailList->name,
        ];

        $data = array_merge($data, $add_datas);

        Log::create([
            'customer_id' => $customer->id,
            'type' => 'page',
            'name' => $name,
            'data' => json_encode($data),
        ]);
    }
}
