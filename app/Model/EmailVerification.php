<?php

namespace Acelle\Model;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    const RESULT_DELIVERABLE = 'deliverable';
    const RESULT_UNDELIVERABLE = 'undeliverable';
    const RESULT_UNKNOWN = 'unknown';
    const RESULT_RISKY = 'risky';

    /**
     * Check if the email address is deliverable
     *
     * @return boolean
     */
    public function isDeliverable()
    {
        return $this->result == self::RESULT_DELIVERABLE;
    }

    /**
     * Check if the email address is undeliverable
     *
     * @return boolean
     */
    public function isUndeliverable()
    {
        return $this->result == self::RESULT_UNDELIVERABLE;
    }

    /**
     * Check if the email address is risky
     *
     * @return boolean
     */
    public function isRisky()
    {
        return $this->result == self::RESULT_RISKY;
    }

    /**
     * Check if the email address is unknown
     *
     * @return boolean
     */
    public function isUnknown()
    {
        return $this->result == self::RESULT_UNKNOWN;
    }

    /**
     * Email verification result types select options.
     *
     * @return array
     */
    public static function resultSelectOptions()
    {
        return [
            ['value' => self::RESULT_DELIVERABLE, 'text' => trans('messages.email_verification_result_' . self::RESULT_DELIVERABLE)],
			['value' => self::RESULT_UNDELIVERABLE, 'text' => trans('messages.email_verification_result_' . self::RESULT_UNDELIVERABLE)],
			['value' => self::RESULT_UNKNOWN, 'text' => trans('messages.email_verification_result_' . self::RESULT_UNKNOWN)],
            ['value' => self::RESULT_RISKY, 'text' => trans('messages.email_verification_result_' . self::RESULT_RISKY)],
        ];
    }
}
