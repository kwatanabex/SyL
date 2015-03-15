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

/** メール処理関連の例外クラス */
require_once 'SyL_MailException.php';
/** メールメッセージクラス */
require_once 'SyL_MailMessage.php';
/** データソース文字列クラス */
require_once dirname(__FILE__) . '/../Util/SyL_UtilDsn.php';

/**
 * メールクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Mail
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_MailAbstract
{
    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * メールメッセージオブジェクトを作成する
     * 
     * @return SyL_MailMessage メールメッセージオブジェクト
     */
    public static function createMessage()
    {
        return new SyL_MailMessage();
    }
}