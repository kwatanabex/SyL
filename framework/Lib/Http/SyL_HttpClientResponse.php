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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * HTTPレスポンスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpClientResponse
{
    /**
     * レスポンスステータス
     *
     * @var string
     */
    private $status = null;
    /**
     * レスポンスHTTPバージョン
     *
     * @var string
     */
    private $version = null;
    /**
     * レスポンスヘッダ
     *
     * @var array
     */
    private $headers = array();
    /**
     * レスポンス本文
     *
     * @var string
     */
    private $body = null;
    /**
     * サーバーエンコーディング
     * 
     * @var string
     */
    protected $server_encoding = '';

    /**
     * コンストラクタ
     * 
     * @param string リクエスト結果
     */
    public function __construct($data)
    {
        $data     = explode(SyL_HttpClient::EOL . SyL_HttpClient::EOL, $data, 2);
        $compress = '';
        foreach (explode(SyL_HttpClient::EOL, $data[0]) as $header) {
            $header = trim($header);
            if (preg_match('/^HTTP\/(1\.[01]) (\d{3} .+)$/', $header, $matches)) {
                $this->version = $matches[1];
                $this->status  = $matches[2];
            } else {
                $tmps = explode(':', $header, 2);
                if (isset($tmps[1])) {
                    $tmps[1] = trim($tmps[1]);
                    switch ($tmps[0]) {
                    case 'Content-Type':
                        if (preg_match('/charset\=(.+)[ ;]?.*/i', $tmps[1], $matches)) {
                            $this->server_encoding = trim($matches[1]);
                        }
                        break;
                    case 'Content-Encoding':
                        $compress = $tmps[1];
                        break;
                    case 'Transfer-Encoding':
                        if (isset($data[1]) && ($tmps[1] == 'chunked')) {
                            $data[1] = self::decodeChunk($data[1]);
                        }
                    }
                } else {
                    $tmps[1] = null;
                }

                if (!isset($this->headers[$tmps[0]])) {
                    $this->headers[$tmps[0]] = array();
                }
                $this->headers[$tmps[0]][] = $tmps[1];
            }
        }

        if (isset($data[1])) {
            switch ($compress) {
            case 'gzip':    $this->body = gzuncompress($data[1]); break;
            case 'deflate': $this->body = gzinflate($data[1]);    break;
            default:        $this->body = $data[1];
            }

            $client_encoding = SyL_HttpClient::getClientEncoding();
            if ($client_encoding) {
                if ($this->server_encoding) {
                    $this->body = mb_convert_encoding($this->body, $client_encoding, $this->server_encoding);
                } else {
                    $this->body = mb_convert_encoding($this->body, $client_encoding);
                }
            }
        }
    }

    /**
     * Transfer-Encoding chunked ボディをデコードする
     * 
     * @param string chunked 文字列
     * @return string chunked デコードした文字列
     */
    private static function decodeChunk($body_in)
    {
        $body_out = '';
        while (true) {
            if (preg_match('/^([0-9a-fA-F]+)[ ]*(\r\n|\r|\n)(.+)$/s', ltrim($body_in), $matches)) {
                if (($body_out != '') && ($matches[1] == '0')) {
                    break;
                }
                $bytes     = hexdec($matches[1]);
                $body_out .= substr($matches[3], 0, $bytes);
                $body_in   = substr($matches[3], $bytes+1);
            } else {
                break;
            }
        }
        return $body_out;
    }

    /**
     * サーバーエンコーディングを取得する
     * 
     * @return string サーバーエンコーディング
     */
    public function getServerEncoding()
    {
        return $this->server_encoding;
    }

    /**
     * HTTPバージョンを取得
     * 
     * @return string HTTPバージョン
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * ステータスラインを取得
     * 
     * @return string ステータスライン
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * ステータスコードを取得
     * 
     * @return string ステータスコード
     */
    public function getStatusCode()
    {
        return substr($this->status, 0, 3);
    }

    /**
     * コンテンツタイプを取得する
     *
     * @return string コンテンツタイプ
     */
    public function getContentType()
    {
        $header = $this->getHeader('Content-Type');
        return isset($header[0]) ? $header[0] : null;
    }

    /**
     * コンテンツ容量を取得する
     *
     * @return int コンテンツ容量
     */
    public function getContentLength()
    {
        $header = $this->getHeader('Content-Length');
        return isset($header[0]) ? $header[0] : null;
    }

    /**
     * リダイレクト先URLを取得する
     * 
     * @return string リダイレクト先URL
     */
    public function getLocation()
    {
        $header = $this->getHeader('Location');
        return isset($header[0]) ? $header[0] : null;
    }

    /**
     * 認証ヘッダを取得する
     * 
     * @return string 認証ヘッダ
     */
    public function getWWWAuthenticate()
    {
        $header = $this->getHeader('WWW-Authenticate');
        return isset($header[0]) ? $header[0] : null;
    }

    /**
     * レスポンスヘッダを確認する
     * 
     * @param string レスポンスヘッダ名
     * @return bool true: レスポンスヘッダあり、false: レスポンスヘッダ無し
     */
    public function isHeader($name)
    {
        $header = $this->getHeader($name);
        return isset($header[0]);
    }

    /**
     * レスポンスヘッダを取得する
     * 
     * @param string レスポンスヘッダ名
     * @return array レスポンスヘッダ
     */
    public function getHeader($name)
    {
        $headers = array();
        foreach ($this->headers as $header_name => $header_values) {
            if ($name == $header_name) {
                $headers = $header_values;
                break;
            }
        }
        return $headers;
    }

    /**
     * 全てのレスポンスヘッダを取得する
     * 
     * @return array 全てのレスポンスヘッダ
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * レスポンス本文を取得する
     * 
     * @return string レスポンス本文
     */
    public function getBody()
    {
        return $this->body;
    }
}
