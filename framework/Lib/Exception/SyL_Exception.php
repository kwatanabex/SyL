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
 * @subpackage SyL.Lib.Exception
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * SyL 汎用例外クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Exception
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_Exception extends Exception
{
    /**
     * コンストラクタ
     *
     * @param string エラーメッセージ
     * @param Exception 直前の例外
     * @param int エラーコード
     */
    public function __construct($message, Exception $previous=null, $code=E_USER_ERROR)
    {
        if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
            parent::__construct($message, $code, $previous);
        } else {
            parent::__construct($message, $code);
        }
    }
}

/** クラスが存在しない場合の例外クラス */
require_once 'SyL_ClassNotFoundException.php';
/** 重複例外クラス */
require_once 'SyL_DuplicateException.php';
/** ファイルが存在しない場合の例外クラス */
require_once 'SyL_FileNotFoundException.php';
/** 有効なクラスでない場合の例外クラス */
require_once 'SyL_InvalidClassException.php';
/** 操作不正時の例外クラス */
require_once 'SyL_InvalidOperationException.php';
/** パラメータ不正時の例外クラス */
require_once 'SyL_InvalidParameterException.php';
/** キーが存在しない場合の例外クラス */
require_once 'SyL_KeyNotFoundException.php';
/** 範囲外の例外クラス */
require_once 'SyL_OutOfRangeException.php';
/** 未実装時の例外クラス */
require_once 'SyL_NotImplementedException.php';
/** 権限なし例外クラス */
require_once 'SyL_PermissionDeniedException.php';
/** 設定値不正時の例外クラス */
require_once 'SyL_InvalidConfigException.php';



