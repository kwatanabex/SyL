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

/**
 * フレームワークカスタムクラス
 *
 * @package    SyL.Core
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_CustomClass
{
    /**
     * カスタムクラス定義
     *
     * @var array
     */
    private static $classes = null;

    /**
     * コンストラクタ
     */
    private function __construct()
    {
    }

    /**
     * 初期化する
     *
     * @param array カスタムクラス定義
     */
    public static function initialize(array $classes)
    {
        if (self::$classes !== null) {
            throw new SyL_InvalidOperationException('this operation only 1');
        }
        self::$classes = $classes;
    }

    /**
     * 認証クラス名を取得する
     * 
     * @return string 認証クラス名
     */
    public static function getAuthenticationClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::AUTHENTICATION_CLASS]) ? self::$classes[SyL_ConfigFileClasses::AUTHENTICATION_CLASS] : null;
    }

    /**
     * データクラス名を取得する
     * 
     * @return string データクラス名
     */
    public static function getDataClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::DATA_CLASS]) ? self::$classes[SyL_ConfigFileClasses::DATA_CLASS] : null;
    }

    /**
     * エラーハンドラクラス名を取得する
     * 
     * @return string エラーハンドラクラス名
     */
    public static function getErrorHandlerClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::ERROR_HANDLER_CLASS]) ? self::$classes[SyL_ConfigFileClasses::ERROR_HANDLER_CLASS] : null;
    }

    /**
     * リクエストクラス名を取得する
     * 
     * @return string リクエストクラス名
     */
    public static function getRequestClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::REQUEST_CLASS]) ? self::$classes[SyL_ConfigFileClasses::REQUEST_CLASS] : null;
    }

    /**
     * レスポンスクラス名を取得する
     * 
     * @return string レスポンスクラス名
     */
    public static function getResponseClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::RESPONSE_CLASS]) ? self::$classes[SyL_ConfigFileClasses::RESPONSE_CLASS] : null;
    }

    /**
     * ルータクラス名を取得する
     * 
     * @return string ルータクラス名
     */
    public static function getRouterClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::ROUTER_CLASS]) ? self::$classes[SyL_ConfigFileClasses::ROUTER_CLASS] : null;
    }

    /**
     * セッションクラス名を取得する
     * 
     * @return string セッションクラス名
     */
    public static function getSessionClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::SESSION_CLASS]) ? self::$classes[SyL_ConfigFileClasses::SESSION_CLASS] : null;
    }

    /**
     * ユーザークラス名を取得する
     * 
     * @return string ユーザークラス名
     */
    public static function getUserClass()
    {
        return isset(self::$classes[SyL_ConfigFileClasses::USER_CLASS]) ? self::$classes[SyL_ConfigFileClasses::USER_CLASS] : null;
    }
}
