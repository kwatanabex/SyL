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
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 設定例外クラス */
require_once 'SyL_ConfigException.php';

/**
 * 設定値保持クラス
 *
 * SyLフレームワークで管理するグローバルなプロパティクラス。
 * ユーザー側からでも自由に使えるが、
 * 設定名に「SYL_」接頭辞がある場合は、final 化し、上書きできない。
 *
 * また、設定値は1度セットすると削除することはできない。
 * 
 * @package    SyL.Core
 * @subpackage SyL.Core.Config
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
final class SyL_Config
{
    /**
     * 設定値配列
     * 
     * @var array
     */
    private static $configs = array();
    /**
     * 初期化完了フラグ
     * 
     * @var array
     */
    private static $complete = false;

    /**
     * コンストラクタ
     */
    private function __construct()
    {
    }

    /**
     * 初期化を完了する
     */
    public static function loadComplete()
    {
        self::$complete = true;
    }

    /**
     * 設定名に対応した設定値をセットする
     *
     * @param string 設定名
     * @param string 設定値
     */
    public static function set($name, $value)
    {
        if (self::$complete) {
            throw new SyL_InvalidOperationException('already initialize complete');
        }
        self::$configs[$name] = $value;
    }

    /**
     * 設定名に対応した設定値を取得する
     *
     * @param string 設定名
     * @return string 設定値
     */
    public static function get($name)
    {
        return isset(self::$configs[$name]) ? self::$configs[$name] : null;
    }

    /**
     * 設定値を全て取得する
     *
     * @return array 設定値配列
     */
    public static function getAll()
    {
        return self::$configs;
    }
}
