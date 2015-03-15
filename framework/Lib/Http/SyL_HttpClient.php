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

/** ソケットクラス */
require_once dirname(__FILE__) . '/../Socket/SyL_Socket.php';
/** HTTPリクエストクラス */
require_once 'SyL_HttpClientRequest.php';
/** HTTPレスポンスクラス */
require_once 'SyL_HttpClientResponse.php';
/** HTTP関連例外クラス */
require_once 'SyL_HttpException.php';

/**
 * HTTPクライアントクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpClient
{
    /**
     * リクエスト対象ホスト名
     * 
     * @var string
     */
    private $host = null;
    /**
     * リクエスト対象ポート番号
     * 
     * @var int
     */
    private $port = 80;
    /**
     * SSL使用判定
     * 
     * @var bool
     */
    private $ssl = false;
    /**
     * タイムアウト [s]
     *
     * var int
     */
    private $timeout = 5;
    /**
     * プロキシホスト
     * 
     * @var string
     */
    private $proxy_host = null;
    /**
     * プロキシポート
     * 
     * @var int
     */
    private $proxy_port = null;
    /**
     * リダイレクト有効化フラグ
     * 
     * @var bool
     */
    private $redirect = false;
    /**
     * リダイレクトループ上限
     * 
     * @var int
     */
    private $redirect_loop_limit = 3;
    /**
     * リダイレクトループカウンタ
     * 
     * @var int
     */
    private $redirect_couter = 0;
    /**
     * リクエストトレースメソッド
     * 
     * @var mixed
     */
    private $callback_trace = null;
    /**
     * クライアントエンコーディング
     * 
     * @var string
     */
    private static $client_encoding = null;
    /**
     * 改行コード
     *
     * @var string
     */
    const EOL = "\r\n";

    /**
     * コンストラクタ
     * 
     * @param string リクエスト対象ホスト名
     * @param int リクエスト対象ポート番号
     * @param int タイムアウト
     * @param bool HTTPS使用判定
     */
    public function __construct($host, $port=80, $timeout=5, $ssl=false)
    {
        $this->host    = $host;
        $this->port    = $port;
        $this->timeout = $timeout;
        $this->ssl     = $ssl;
    }

    /**
     * ホスト名を取得する
     * 
     * @return string ホスト名
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * ポート番号を取得する
     * 
     * @return int ポート番号
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * HTTPSを使用するか判定する
     * 
     * @return bool true: HTTPS使用する、false: HTTPS使用しない
     */
    public function isHttps()
    {
        return $this->ssl;
    }

    /**
     * クライアントエンコーディングをセットする
     * 
     * @param string クライアントエンコーディング
     */
    public static function setClientEncoding($client_encoding)
    {
        self::$client_encoding = $client_encoding;
    }

    /**
     * クライアントエンコーディングを取得する
     * 
     * @return string クライアントエンコーディング
     */
    public static function getClientEncoding()
    {
        return self::$client_encoding;
    }

    /**
     * プロキシ接続情報をセットする
     * 
     * @param string プロキシホスト
     * @param int プロキシポート
     */
    public function setProxy($proxy_host, $proxy_port)
    {
        $this->proxy_host = $proxy_host;
        $this->proxy_port = $proxy_port;
    }

    /**
     * リクエストオブジェクトを取得する
     * 
     * @param string リクエストURL
     * @param string リクエストメソッド
     * @param string HTTPバージョン
     */
    public function createRequest($path='/', $method='GET', $version='1.1')
    {
        $request = new SyL_HttpClientRequest($path, $method, $version);
        if ((float)$version >= 1.1) {
            $request->setHost($this->host);
        }
        return $request;
    }

    /**
     * リダイレクト追跡リクエストを発行判定を適用する
     * 
     * @param bool リダイレクト追跡リクエストを発行判定
     * @param int リダイレクトループ上限
     */
    public function applyRedirect($redirect, $redirect_loop_limit=3)
    {
        $this->redirect = $redirect;
        $this->redirect_loop_limit = $redirect_loop_limit;
    }

    /**
     * 通信コールバックメソッドをセットする
     * 
     * @param mixed コールバックメソッド
     * @throws SyL_InvalidParameterException 引数が関数／メソッド以外の場合
     */
    public function setCallbackTrace($callback_trace)
    {
        if (!is_callable($callback_trace)) {
            throw new SyL_InvalidParameterException('invalid callable parameter');
        }
        $this->callback_trace = $callback_trace;
    }

    /**
     * リクエストを送信して、レスポンスオブジェクトを取得する
     * 
     * @param SyL_HttpRequest リクエストパラメータオブジェクト
     * @return SyL_HttpClientResponse レスポンスオブジェクト
     * @throws SyL_HttpRedirectLimitException リダイレクトの上限を超えた場合
     * @throws SyL_HttpRedirectUrlException リダイレクトURLが絶対パスでない場合
     */
    public function sendRequest(SyL_HttpClientRequest $request)
    {
        $dsn = ($this->ssl) ? 'ssl://%s:%s' : 'tcp://%s:%s';
        if ($this->proxy_host && $this->proxy_port) {
            $dsn = sprintf($dsn, $this->proxy_host, $this->proxy_port);
        } else {
            $dsn = sprintf($dsn, $this->host, $this->port);
        }

        $socket = null;
        $data = null;
        try {
            $socket = new SyL_Socket($dsn);
            if ($this->callback_trace) {
                $socket->setCommandCallback($this->callback_trace);
            }
            $socket->open($this->timeout, $this->timeout);
            $socket->send($request->getSource());

            $data = $socket->receiveAll(true);
            $socket->close();
            $socket = null;

        } catch (Exception $e) {
            if ($socket instanceof SyL_Socket) {
                $socket->close();
            }
            $socket = null;
            throw $e;
        }

        $response = $this->createResponse($data);

        if ($request->getAuthorization() instanceof SyL_HttpAuthenticationDigest) {
            static $digest = false;
            if (!$digest) {
                $www_authenticate = $response->getWWWAuthenticate();
                if ($www_authenticate) {
                    $values = self::parseHeaderValue($www_authenticate);
                    if (isset($values[0]) && ($values[0] == 'Digest')) {
                        $digest_request = clone $request;
                        $auth = $digest_request->getAuthorization();
                        $auth->setRealm($values['realm']);
                        $auth->setNonce($values['nonce']);
                        $auth->setAlgorithm($values['algorithm']);
                        $auth->setQop($values['qop']);
                        $auth->setUri($request->getUri());
                        $auth->setMethod($request->getMethod());
                        $digest_request->setAuthorization($auth);
                        $digest = true;
                        $response = $this->sendRequest($digest_request);
                    }
                }
            }
            $digest = false;
        }

        if ($this->redirect && preg_match('/^30[12]/', $response->getStatus())) {
            $url = $response->getLocation();
            if ($url) {
                if ($this->redirect_couter >= $this->redirect_loop_limit) {
                    throw new SyL_HttpRedirectLimitException("redirect loop limit error ({$this->redirect_loop_limit})");
                }
                if (preg_match('/^https?:\/\//', $url)) {
                    $urls = parse_url($url);

                    $this->host = $urls['host'];
                    if (isset($urls['port'])) {
                        $this->port = $urls['port'];
                    } else {
                        $this->port = ($urls['scheme'] == 'https') ? '443' : '80';
                    }

                    if ($this->proxy_host && $this->proxy_port) {
                        // use proxy
                    } else {
                        $url = $urls['path'];
                        if (isset($urls['query'])) {
                            $url .= '?' . $urls['query'];
                        }
                        if (isset($urls['fragment'])) {
                            $url .= '#' . $urls['fragment'];
                        }
                    }
                } else {
                    throw new SyL_HttpRedirectUrlException("Location header not absolute url ({$url})");
                }

                $redirect_request = clone $request;
                $redirect_request->setUrl($url, 'GET');
                $redirect_request->setHost($this->host);

                $this->redirect_couter++;
                $response = $this->sendRequest($redirect_request);
            }
        }

        return $response;
    }

    /**
     * レスポンスオブジェクトを作成する
     * 
     * @param string HTTPレスポンス
     * @return SyL_HttpClientResponse レスポンスオブジェクト
     */
    protected function createResponse($data)
    {
        return new SyL_HttpClientResponse($data);
    }

    /**
     * ヘッダ値をパースし、配列に変換する
     *
     * @param string ヘッダ値
     * @return array パース後ヘッダ値配列
     */
    public static function parseHeaderValue($header_value)
    {
        $values = array();
        $first = true;
        foreach (preg_split('/[;,]/', $header_value) as $value) {
            $elems = explode('=', trim($value), 2);
            if (isset($elems[1])) {
                if (preg_match('/^"(.*)"$/', trim($elems[1]), $matches)) {
                    $elems[1] = $matches[1];
                }
            }

            if ($first) {
                $elems2 = explode(' ', $elems[0], 2);
                if (isset($elems2[1])) {
                    $values[] = $elems2[0];
                    $elems[0] = $elems2[1];
                }
            }
            
            if (isset($elems[1])) {
                $values[$elems[0]] = $elems[1];
            } else {
                $values[] = $elems[0];
            }
            $first = false;
        }
        return $values;
    }
}
