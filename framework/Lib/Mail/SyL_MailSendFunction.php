<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
 *
 * PHP version 5 (>= 5.2.10)
 *
 * Copyright (C) 2006-2011 k.watanabe
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * -----------------------------------------------------------------------------
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

require_once 'SyL_MailSendAbstract.php';

/**
 * メール関数送信クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_MailSendFunction extends SyL_MailSendAbstract
{
    /**
     * メール送信実行
     *
     * @param SyL_MailMessage メールメッセージオブジェクト
     */
    public function send(SyL_MailMessage $message)
    {
        $from    = $message->getFrom();
        $subject = $message->convertEncoding($message->getSubject(), true);
        $msg     = $message->getMessage(true);

        $tos = array();
        foreach ($message->getTo() as $to) {
            $tos[] = $message->convertEncodingAddress($to[0], $to[1]);
        }
        $to = implode(', ', $tos);

        $additional_parameters = ' -f' . $from[0]; // $message->getMessage で from と to のチェック済み

        try {
            if (!mail($to, $subject, null, $msg, $additional_parameters)) {
                throw new Exception('mail function error');
            }
        } catch (Exception $e) {
            throw new SyL_MailSendException($e->getMessage());
        }
    }
}
