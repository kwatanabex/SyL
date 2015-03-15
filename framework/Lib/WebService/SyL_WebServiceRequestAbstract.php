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
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用プロパティクラス */
require_once dirname(__FILE__) . '/../SyL_Property.php';

/**
 * WEBサービスリクエストクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_WebServiceRequestAbstract extends SyL_Property
{
    /**
     * リクエストタイプ
     *
     * @var string
     */
    protected $type = 'rest';
    /**
     * SSL
     *
     * @var bool
     */
    protected $ssl = false;
    /**
     * WEBサービスサーバーのホスト名
     *
     * @var string
     */
    protected $host = '';
    /**
     * WEBサービスサーバーのポート
     *
     * @var int
     */
    protected $port = 80;
    /**
     * WEBサーバーの接続タイムアウト
     *
     * @var int
     */
    protected $timeout = 5;
    /**
     * リクエストメソッド
     *
     * @var string
     */
    protected $method = 'GET';
    /**
     * 実行URLパス
     *
     * @var string
     */
    protected $path = '/';
    /**
     * 実行URLパス
     *
     * @var string
     */
    private $response_class = '';

    /**
     * コンストラクタ
     *
     * @param string レスポンスクラス名
     */
    public function __construct($response_class)
    {
        $this->response_class = $response_class;
    }

    /**
     * プロパティをセットする
     * 
     * @param string プロパティ名
     * @param string プロパティ値
     */
    public function __set($name, $value) 
    {
        $method_name = 'set' . ucfirst($name);
        if (method_exists($this, $method_name)) {
            $this->$method_name($value);
        } else {
            throw new SyL_InvalidParameterException("invalid property. setter method not found ({$name})");
        }
    }

    /**
     * リクエストタイプを取得する
     *
     * @return string リクエストタイプ
     */
    public function getRequestType()
    {
        return $this->type;
    }

    /**
     * SSLを有効か判定する
     *
     * @return bool SSLの有無
     */
    public function isSsl()
    {
        return $this->ssl;
    }

    /**
     * リクエストホストを取得する
     *
     * @return string リクエストホスト
     */
    public function getRequestHost()
    {
        return $this->host;
    }

    /**
     * リクエストポートを取得する
     *
     * @return int リクエストポート
     */
    public function getRequestPort()
    {
        return $this->port;
    }

    /**
     * 接続タイムアウトを取得する
     *
     * @return int 接続タイムアウト
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * リクエストメソッドを取得する
     *
     * @return string リクエストメソッド
     */
    public function getRequestMethod()
    {
        return $this->method;
    }

    /**
     * リクエストパスを取得する
     *
     * @return string リクエストパス
     */
    public function getRequestPath()
    {
        return $this->path;
    }

    /**
     * レスポンスクラスを取得する
     *
     * @return string レスポンスクラス
     */
    public function getResponseClass()
    {
        return $this->response_class;
    }

    /**
     * リクエスト内容のチェック
     *
     * @throws SyL_InvalidParameterException リクエストパラメータにエラーがある場合
     */
    public function validate()
    {
    }
}
