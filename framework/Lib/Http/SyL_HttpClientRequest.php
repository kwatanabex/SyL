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
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** 汎用文字列変換クラス */
require_once dirname(__FILE__) . '/../Util/SyL_UtilConverter.php';

/**
 * HTTPリクエストクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpClientRequest
{
    /**
     * HTTPリクエスト対象URL
     *
     * @var string
     */
    private $path;
    /**
     * HTTPバージョン
     *
     * @var string
     */
    private $version = '1.1';
    /**
     * HTTPリクエストメソッド
     *
     * @var string
     */
    private $method = 'GET';
    /**
     * リクエストヘッダ配列
     *
     * @var string
     */
    private $headers = array();
    /**
     * リクエストパラメータ配列
     *
     * @var string
     */
    private $parameters = array();
    /**
     * リクエストボディ
     *
     * @var string
     */
    private $body = null;
    /**
     * 認証オブジェクト
     *
     * @var SyL_HttpAuthenticationAbstract
     */
    private $auth = null;
    /**
     * デフォルトユーザーエージェント
     *
     * var string
     */
    const DEFAULT_USERAGENT = 'SyL Framework/HttpClient';

    /**
     * コンストラクタ
     * 
     * @param string HTTPリクエスト対象URL
     * @param string HTTPリクエストメソッド
     * @param string HTTPバージョン
     */
    public function __construct($path, $method='GET', $version='1.1')
    {
        $this->path = $path;

        if ($method) {
            $method = strtoupper($method);
            switch ($method) {
            case 'GET':
            case 'HEAD':
            case 'POST':
            case 'PUT':
            case 'DELETE':
            case 'TRACE':
            case 'CONNECT':
            case 'LINK':
            case 'UNLINK':
            case 'OPTIONS':
            case 'PATCH':
                break;
            default:
                throw new SyL_InvalidParameterException("invalid HTTP method ({$method})");
            }
            $this->method  = $method;
        }

        if ($version) {
            switch ($version) {
            case '0.9':
            case '1.0':
            case '1.1':
                break;
            default:
                throw new SyL_InvalidParameterException("invalid HTTP version ({$version})");
            }
            $this->version = $version;
        }

        $this->setHeader('Accept', '*/*');
        $this->setUserAgent(self::DEFAULT_USERAGENT);
        if ((float)$version >= 1.1) {
            $this->setHeader('Connection', 'close');
        }
        if ($this->method == 'POST') {
            $this->setContentType('application/x-www-form-urlencoded');
        }
    }

    /**
     * HTTPメソッドを取得する
     * 
     * @return string HTTPメソッド
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * HTTPリクエスト対象URLを取得する
     * 
     * @return string HTTPリクエスト対象URL
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * URIを取得する
     * 
     * @return string URI
     */
    public function getUri()
    {
        $uri = '';
        switch ($this->method) {
        case 'POST':
        case 'PUT':
            $uri = $this->path;
            break;
        default:
            $uri = $this->path;
            $parameter = $this->buildParameter();
            if ($parameter) {
                $uri .= '?' . $parameter;
            }
        }
        return $uri;
    }

    /**
     * ヘッダをセットする
     * 
     * @param string ヘッダ名
     * @param string ヘッダ値
     */
    public function setHeader($name, $value)
    {
        $this->headers[$name] = array();
        $this->headers[$name][] = $value;
    }

    /**
     * ヘッダを追加する
     * 
     * @param string ヘッダ名
     * @param string ヘッダ値
     */
    public function addHeader($name, $value)
    {
        if (!isset($this->headers[$name])) {
            $this->headers[$name] = array();
        }
        $this->headers[$name][] = $value;
    }

    /**
     * ヘッダがセットされているか確認する
     * 
     * @param string ヘッダ名
     * @return bool true: ヘッダセット済み、false: ヘッダ未セット
     */
    public function isHeader($name)
    {
        return isset($this->headers[$name]);
    }

    /**
     * ホストをセットする
     * 
     * @param string ホスト
     */
    public function setHost($value)
    {
        $this->setHeader('Host', $value);
    }

    /**
     * アクセストをセットする
     * 
     * @param string アクセスト
     */
    public function setAccept($value)
    {
        $this->setHeader('Accept', $value);
    }

    /**
     * コンテンツタイプをセットする
     * 
     * @param string コンテンツタイプ
     */
    public function setContentType($value)
    {
        $this->setHeader('Content-Type', $value);
    }

    /**
     * ユーザーエージェントをセットする
     * 
     * @param string ユーザーエージェント
     */
    public function setUserAgent($value)
    {
        $this->setHeader('User-Agent', $value);
    }

    /**
     * 認証オブジェクトをセットする
     *
     * @param SyL_HttpAuthenticationAbstract 認証オブジェクト
     */
    public function setAuthorization(SyL_HttpAuthenticationAbstract $auth)
    {
        $this->auth = $auth;
    }

    /**
     * 認証オブジェクトを取得する
     *
     * @return SyL_HttpAuthenticationAbstract 認証オブジェクト
     */
    public function getAuthorization()
    {
        return $this->auth;
    }

/*    
    chunk request
    request body binary or xml
    file upload(multipart)
    
    */

    /**
     * パラメータをセットする
     * 
     * @param string パラメータ名
     * @param string パラメータ値
     */
    public function setParameter($name, $value)
    {
        if (is_array($value)) {
            foreach ($value as $value1) {
                $this->setParameter($name, $value1);
            }
        } else {
            if (!isset($this->parameters[$name])) {
                $this->parameters[$name] = array();
            }
            $this->parameters[$name][] = $value;
        }
    }

    /**
     * パラメータを取得する
     * 
     * @param string パラメータ名
     * @return array パラメータ値の配列
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * 全パラメータを取得する
     * 
     * @return array 全パラメータ
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * パラメータを削除する
     * 
     * @param string パラメータ名
     */
    public function removeParameter($name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * パラメータをリクエスト形式に変換する
     * 
     * @return string パラメータ文字列
     */
    protected function buildParameter()
    {
        $parameters = array();
        foreach ($this->parameters as $name => $values) {
            foreach ($values as $value) {
                $parameters[] = SyL_UtilConverter::encodeUrlToRfc3986($name) . '=' .  SyL_UtilConverter::encodeUrlToRfc3986($value);
            }
        }
        return implode('&', $parameters);
    }

    /**
     * リクエストボディをセットする
     * 
     * @param string リクエストボディ
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * HTTP通信情報を取得する
     * 
     * @return string HTTP通信情報
     */
    public function getSource()
    {
        $path      = $this->path;
        $method    = $this->method;
        $version   = $this->version;
        $parameter = $this->buildParameter();

        $body = null;
        switch ($method) {
        case 'POST':
        case 'PUT':
            if ($this->body) {
                $body = $this->body;
            } else {
                $body = $parameter;
            }
            if (!$this->isHeader('Content-Length') && !$this->isHeader('Transfer-Encoding')) {
                $this->setHeader('Content-Length', strlen($body));
            }
            break;
        default:
            if ($parameter) {
                 $path .= '?' . $parameter;
            }
        }

        if ($this->auth) {
            $value = $this->auth->getHeaderValue();
            if ($value) {
                $this->setHeader($this->auth->getHeaderName(), $value);
            }
        }

        $source  = "";
        $source .= "{$method} {$path} HTTP/{$version}" . SyL_HttpClient::EOL;
        foreach ($this->headers as $name => $values) {
            foreach ($values as $value) {
                $source .= "{$name}: $value" . SyL_HttpClient::EOL;
            }
        }
        $source .= SyL_HttpClient::EOL;
        if ($body) {
            $source .= $body;
        }
        return $source;
    }
}
