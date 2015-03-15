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
 * @subpackage SyL.Core.Response
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** レスポンス例外クラス */
require_once 'SyL_ResponseException.php';

/**
 * レスポンスクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Response
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ResponseAbstract implements SyL_ContainerComponentInterface
{
    /**
     * レスポンスキャッシュ有効時間
     * 0 はキャッシュ無し
     * 
     * @var int
     */
    protected $response_cache_time = 0;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * レスポンスオブジェクトを取得する
     * 
     * @return SyL_ResponseAbstract レスポンスオブジェクト
     */
    public static function getInstance()
    {
        static $singleton = null;
        if ($singleton == null) {
            $name = SyL_CustomClass::getResponseClass();
            if ($name) {
                $classname = SyL_Loader::userLib($name);
            } else {
                $classname = 'SyL_Response' . SYL_APP_TYPE;
                include_once $classname . '.php';
            }

            if (!is_subclass_of($classname, __CLASS__)) {
                throw new SyL_InvalidClassException("invalid component class `{$classname}'. not extends `" . __CLASS__ . "' class");
            }
            $singleton = new $classname();
        }
        return $singleton;
    }

    /**
     * レスポンスキャッシュ有効時間をセットする
     * 
     * @param int レスポンスキャッシュ有効時間
     */
    public function setResponseCacheTime($response_cache_time)
    {
        $this->response_cache_time = $response_cache_time;
    }

    /**
     * 表示情報を出力
     * 
     * @param SyL_ViewAbstract 表示オブジェクト
     */
    public abstract function display(SyL_ViewAbstract $view);

    /**
     * キャッシュ情報を出力する
     *
     * キャッシュ情報があれば、通信は終了する。
     */
    public abstract function displayCache();
}
