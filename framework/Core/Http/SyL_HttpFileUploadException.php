<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * ファイルアップロード例外クラス
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpFileUploadException extends SyL_Exception
{
    /**
     * ファイルアップロードエラーコード
     *
     * @var int
     */
    private $upload_error_code = null;

    /**
     * コンストラクタ
     *
     * @param string エラーメッセージ
     * @param Exception 直前の例外
     * @param int エラーコード
     */
    public function __construct($message, $upload_error_code, Exception $previous=null, $code=E_USER_ERROR)
    {
        parent::__construct($message, $previous, $code);
        $this->upload_error_code = $upload_error_code;
    }

    /**
     * ファイルアップロードエラーコードを取得する
     *
     * @return int ファイルアップロードエラーコード
     */
    public function getUploadErrorCode()
    {
        return $this->upload_error_code;
    }
}
