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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用プロパティクラス（パラメータ固定） */
require_once SYL_FRAMEWORK_DIR . '/Lib/SyL_FixedProperty.php';

/**
 * ユーザークラス
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_UserAbstract extends SyL_FixedProperty
{
    /**
     * ユーザーID
     *
     * @var string
     */
    private $userid = null;
    /**
     * ユーザー名
     *
     * @var string
     */
    private $username = null;
    /**
     * セッションのキー名
     *
     * @var string
     */
    const SESSION_KEY = '__syl_user';

    /**
     * コンストラクタ
     *
     * @param string ユーザーID
     * @param string ユーザー名
     */
    private function __construct($userid, $username=null)
    {
        $this->userid = $userid;
        $this->username = $username;
    }

    /**
     * ユーザーオブジェクトを作成する
     * 
     * @param string ユーザーID
     * @param string ユーザー名
     * @return SyL_User ユーザーオブジェクト
     */
    public static function createInstance($userid, $username=null)
    {
        static $classname = null;
        if ($classname == null) {
            $classname = SyL_Loader::userLib(SyL_CustomClass::getUserClass());
        }
        return new $classname($userid, $username);
    }

    /**
     * ユーザーIDを取得
     * 
     * @return string ユーザーID
     */
    public function getId()
    {
        return $this->userid;
    }

    /**
     * ユーザー名を取得
     * 
     * @return string ユーザー名
     */
    public function getName()
    {
        return $this->username;
    }

}
