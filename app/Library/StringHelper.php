<?php

/**
 * StringHelper class.
 *
 * Provide string helper methods
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   Acelle Library
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

namespace Acelle\Library;

class StringHelper
{
    /**
     * Custom base64 encoding. Replace unsafe url chars.
     *
     * @param string $val
     *
     * @return string
     */
    public static function base64UrlEncode($val)
    {
        return strtr(base64_encode($val), '+/=', '-_,');
    }

    /**
     * Custom base64 decode. Replace custom url safe values with normal
     * base64 characters before decoding.
     *
     * @param string $val
     *
     * @return string
     */
    public static function base64UrlDecode($val)
    {
        return base64_decode(strtr($val, '-_,', '+/='));
    }

    /**
     * Custom base64 decode. Replace custom url safe values with normal
     * base64 characters before decoding.
     *
     * @param string $val
     *
     * @return string
     */
    public static function cleanupMessageId($msgId)
    {
        return preg_replace('/[<>\s]*/', '', $msgId);
    }

    /**
     * Custom base64 decode. Replace custom url safe values with normal
     * base64 characters before decoding.
     *
     * @param string $val
     *
     * @return string
     */
    public static function getDomainFromEmail($email)
    {
        return substr(strrchr($email, '@'), 1);
    }

    /**
     * Generate MessageId from domain name
     *
     * @param string $val
     *
     * @return string
     */
    public static function generateMessageId($domain, $test = false)
    {
        // @note: be careful when changing the message format, it may impact some other parts of the application
        // For example, see the DeliveryHandler::getMessageId()
        if ($test) {
            // generate a test MessageId for a test email
            // then replace the uniqid() with 0*13
            return time().rand(100000, 999999).'.0000000000000@'.$domain;
        } else {
            return time().rand(100000, 999999).'.'.uniqid().'@'.$domain;
        }
    }

    /**
     * Check if a given string is a test Message Id
     *
     * @param string $messageId
     *
     * @return boolean
     */
    public static function isTestMessageId($messageId)
    {
        return (strpos($messageId, '.0000000000000@') !== false);
    }

    /**
     * Custom base64 decode. Replace custom url safe values with normal
     * base64 characters before decoding.
     *
     * @param string $val
     *
     * @return string
     */
    public static function joinUrl()
    {
        $array = array_map(function ($e) {
            return preg_replace('/(^\/+|\/+$)/', '', $e);
        }, func_get_args());

        return implode('/', $array);
    }

    /**
     * Extract SendGrid X-Message-Id from Smtp-Id
     * For example, extract "GuUFV1znQTmkQyPXrPLyxA" from "<GuUFV1znQTmkQyPXrPLyxA@ismtpd0019p1sin1.sendgrid.net>"
     *
     * @param string $val
     *
     * @return string
     */
    public static function extractSendGridMessageId($smtpId) {
        $cleaned = self::cleanupMessageId($smtpId);
        return substr($cleaned, 0, strpos($cleaned, '@'));
    }
}
