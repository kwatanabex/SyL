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
 * @subpackage SyL.Core.Context
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 遷移情報クラス */
require_once SYL_FRAMEWORK_DIR . '/Core/Router/SyL_RouterAbstract.php';

/**
 * フレームワークフィールド情報管理クラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Context
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_ContextAbstract implements SyL_ContainerComponentInterface
{
    /**
     * 遷移情報オブジェクト
     * 
     * @var SyL_RouterAbstract
     */
    protected $router = null;
    /**
     * デフォルトビュークラス
     *
     * @var string
     */
    protected $default_view_class = '';
    /**
     * ビューパラメータ
     *
     * @var array
     */
    protected $view_parameters = array();
    /**
     * DBコネクション
     *
     * @var array
     */
    private static $connections = array();

    /**
     * コンストラクタ
     *
     * @param SyL_Data データオブジェクト
     */
    protected function __construct(SyL_Data $data)
    {
        try {
            $this->router = SyL_RouterAbstract::createInstance($data);
        } catch (SyL_RouterNotFoundException $e) {
            throw new SyL_ResponseNotFoundException(get_class($e) . ': ' . $e->getMessage(), E_NOTICE, $e);
        } catch (SyL_RouterInvalidPathException $e) {
            throw new SyL_ResponseNotFoundException(get_class($e) . ': ' . $e->getMessage(), E_NOTICE, $e);
        }
        if (!$this->router->getViewClass()) {
            $this->router->setViewClass($this->default_view_class);
        }
    }

    /**
     * コンテキストオブジェクトを作成する
     *
     * @param SyL_Data データオブジェクト
     * @return SyL_ContextAbstract フレームワークフィールド情報オブジェクト
     */
    public static function createInstance(SyL_Data $data)
    {
        $class_name = 'SyL_Context' . SYL_APP_TYPE;
        include_once $class_name . '.php';
        return new $class_name($data);
    }

    /**
     * リクエストオブジェクトを取得する
     *
     * @return SyL_RequestAbstract リクエストオブジェクト
     */
    public function getRequest()
    {
        return SyL_RequestAbstract::getInstance();
    }

    /**
     * レスポンスオブジェクトを取得する
     *
     * @return SyL_ResponseAbstract レスポンスオブジェクト
     */
    public function getResponse()
    {
        return SyL_ResponseAbstract::getInstance();
    }

    /**
     * 遷移情報オブジェクトを取得する
     *
     * @return SyL_RouterAbstract 遷移情報オブジェクト
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * ビューパラメータを取得する
     *
     * @param string ビューパラメータ名
     * @return mixed ビューパラメータ値
     */
    public function getViewParameter($name)
    {
        return isset($this->view_parameters[$name]) ? $this->view_parameters[$name] : null;
    }

    /**
     * パス一致文字列を取得する
     *
     * @param int 一致番号
     * @return string パス一致文字列
     */
    public function getPathMatch($index)
    {
        $match_parameter = $this->router->getPathMatches();
        return isset($match_parameter[$index]) ? $match_parameter[$index] : null;
    }


    // -----------------------------------------------------
    // キャッシュ共通メソッド
    // -----------------------------------------------------

    /**
     * キャッシュを取得する
     *
     * @param string キャッシュのキー
     * @param int キャッシュのライフタイム
     * @return mixed キャッシュ値
     * @throws SyL_InvalidParameterException キャッシュが有効でない場合
     */
    public function getCache($key, $lifetime=3600)
    {
        if (SYL_CACHE) {
            return SyL_CacheStorageAbstract::getInstance()->getAppCache($key, $lifetime);
        } else {
            throw new SyL_InvalidParameterException("cache constant `SYL_CACHE' not enabled");
        }
    }

    /**
     * キャッシュをセットする
     *
     * @param string キャッシュのキー
     * @param mixed キャッシュデータ
     * @param int キャッシュのライフタイム
     * @throws SyL_InvalidParameterException キャッシュが有効でない場合
     */
    public function setCache($key, $data, $lifetime=3600)
    {
        if (SYL_CACHE) {
            SyL_CacheStorageAbstract::getInstance()->setAppCache($key, $data, $lifetime);
        } else {
            throw new SyL_InvalidParameterException("cache constant `SYL_CACHE' not enabled");
        }
    }

    // -----------------------------------------------------
    // DB関連共通メソッド
    // -----------------------------------------------------

    /**
     * DBオブジェクトを取得する
     *
     * @param string 接続文字列
     * @return SyL_DbAbstract DBオブジェクト
     * @throws SyL_InvalidParameterException 接続文字列が設定されていない、または設定ファイルに SYL_DB_CONNECTION_STRING が設定されていない場合
     */
    public function getDB($dsn=null)
    {
        if (!$dsn) {
            $dsn = SyL_Config::get('SYL_DB_CONNECTION_STRING');
            if (!$dsn) {
                throw new SyL_InvalidParameterException('DB connection string not found');
            }
        }

        if (!isset(self::$connections[$dsn])) {
            include_once SYL_FRAMEWORK_DIR . '/Lib/Db/SyL_DbAbstract.php';
            SyL_Logger::trace('context.getDB call : database connection start');

            self::$connections[$dsn] = SyL_DbAbstract::getInstance($dsn);
            self::$connections[$dsn]->setCallbackSql(array('SyL_Logger', 'debug'));
        }
        return self::$connections[$dsn];
    }

    /**
     * DBオブジェクトの接続を開放する
     *
     * @param string 接続文字列
     * @throws SyL_InvalidParameterException 接続文字列が設定されていない、または設定ファイルに SYL_DB_CONNECTION_STRING が設定されていない場合
     */
    public function closeDB($dsn=null)
    {
        if (!$dsn) {
            $dsn = SyL_Config::get('SYL_DB_CONNECTION_STRING');
            if (!$dsn) {
                throw new SyL_InvalidParameterException('DB connection string not found');
            }
        }

        if (isset(self::$connections[$dsn])) {
            SyL_Logger::trace('context.closeDB call: database connection close');
            self::$connections[$dsn]->close();
            unset(self::$connections[$dsn]);
        }
    }
}
