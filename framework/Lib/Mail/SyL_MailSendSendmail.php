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
 * Sendmailメール送信クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_MailSendSendmail extends SyL_MailSendAbstract
{
    /**
     * Sendmailパス
     *
     * @var string
     */
    private $sendmail_path = '/usr/lib/sendmail -t -i';

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
        parent::__construct();
        $sendmail_path = ini_get('sendmail_path');
        if ($sendmail_path) {
            $this->setPath($sendmail_path);
        }
    }

    /**
     * Sendmailコマンドパスをセット
     * 
     * @param string Sendmailコマンドパス
     */
    public function setPath($sendmail_path)
    {
        $this->sendmail_path = $sendmail_path;
    }

    /**
     * メール送信実行
     *
     * @param SyL_MailMessage メールメッセージオブジェクト
     */
    public function send(SyL_MailMessage $message)
    {
        $from = $message->getFrom();
        $msg  = $message->getMessage();

        // リターンパスのセット
        $additional_parameters = ' -f ' . $from[0]; // $message->getMessage で from と to のチェック済み

        // メール送信実行
        $pmail = null;
        try {
            $pmail = popen($this->sendmail_path . $additional_parameters, 'w');
            if (!$pmail) {
                throw new SyL_MailSendException("popen function error for sendmail command ({$this->sendmail_path})");
            }
            fputs($pmail, $msg);
            pclose($pmail);
        } catch (Exception $e) {
            if (is_resource($pmail)) {
                pclose($pmail);
            }
            $pmail = null;
            throw new SyL_MailSendException($e->getMessage());
        }
    }
}
