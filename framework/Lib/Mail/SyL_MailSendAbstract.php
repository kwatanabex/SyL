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

/** メールクラス */
require_once 'SyL_MailAbstract.php';

/**
 * メール送信クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_MailSendAbstract extends SyL_MailAbstract
{
    /**
     * メールサーバーのポート番号
     *
     * @var int
     */
    protected $port = 25;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * メール送信クラスのインスタンス取得
     *
     * @param string メール送信文字列
     * @return SyL_MailSendAbstract メール送信オブジェクト
     */
    public static function createInstance($dsn='function')
    {
        list($scheme, $user, $password, $hostname, $port, $path, $query, $fragment) = SyL_UtilDsn::parse($dsn);

        $classname = 'SyL_MailSend' . ucfirst($scheme);
        include_once $classname . '.php';
        $mail = new $classname();
        switch ($scheme) {
        case 'smtp':
            if ($hostname) {
                $mail->setHost($hostname);
            }
            if ($port) {
                $mail->setPort($port);
            }
            if ($user) {
                $mail->setUser($user);
            }
            if ($password) {
                $mail->setPasswd($password);
            }
            break;
        case 'sendmail':
            if ($path) {
                $mail->setPath($path);
            }
            break;
        }

        return $mail;
    }

    /**
     * メール送信実行
     *
     * @param SyL_MailMessage メールメッセージオブジェクト
     */
    public abstract function send(SyL_MailMessage $message);
}
