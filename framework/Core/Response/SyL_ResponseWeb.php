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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * Webレスポンスクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Response
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_ResponseWeb extends SyL_ResponseAbstract
{
    /**
     * 追加HTTPレスポンスヘッダ
     * 
     * @var array
     */
    protected $headers = array();
    /**
     * Contents-Lengthヘッダ出力フラグ
     *
     * @var bool
     */
    protected $enable_content_length = false;
    /**
     * 使用可能ならHTTPレスポンスにgzip圧縮を使用する
     * 1-9 - 使用する（gzip圧縮レベルに対応）
     * 0   - 使用しない
     * 
     * @var int
     */
    protected $gzip_compress = 0;

    /**
     * リダイレクト
     * 
     * @param string リダイレクト先URL
     * @param string レスポンスステータスコード
     */
    public function redirect($url, $code='301')
    {
        if (!preg_match('/^[a-z]+:\/\/(.+)/i', $url)) {
            $protocol = 'http';
            if (isset($_SERVER['HTTPS'])) {
                $protocol = 'https';
            } else if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == '443')) {
                $protocol = 'https';
            }

            $server = '';
            if (isset($_SERVER['HTTP_HOST'])) {
                $server = $_SERVER['HTTP_HOST'];
            } else if (isset($_SERVER['SERVER_NAME'])) {
                $server = $_SERVER['SERVER_NAME'];
                if (isset($_SERVER['SERVER_PORT'])) {
                    $server .= ':' . $_SERVER['SERVER_PORT'];
                }
            }

            if ($protocol && $server) {
                if ($url[0] == '/') {
                    $url = "{$protocol}://{$server}{$url}";
                } else {
                    $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
                    if (!$path) {
                        $path = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null;
                    }
                    $pos = strpos($path, '?');
                    if ($pos !== false) {
                        $path = substr($path, 0, $pos);
                    }

                    if (($path !== '') && (substr($path, -1) != '/')) {
                        $path = dirname($path) . '/';
                    }
                    if ($path) {
                        $path .= $url;
                        while (preg_match('/^(.+\/)([^\/]+\/\.\.\/)(.+)$/', $path, $matches)) {
                            $path = $matches[1] . $matches[3];
                        }
                        $path = str_replace('./', '', $path);
                        $url = "{$protocol}://{$server}{$path}";
                    }
                }
            }
        }

        self::cleanBuffer();

        SyL_Logger::info("Redirect {$code} -> {$url}");
        $this->setStatusHeader($code);
        $this->setHeader('Location', $url);
        $this->sendHeader();

        exit;
    }

    /**
     * HTTPレスポンスステータスをセットする
     * 
     * @param int ステータスコード
     */
    public function setStatusHeader($status_code)
    {
        $this->setHeader(self::getStatusHeader($status_code));
    }

    /**
     * HTTPレスポンスヘッダをセットする
     * 
     * @param string ヘッダ名
     * @param string ヘッダ値
     * @param bool 既存値上書きフラグ
     */
    public function setHeader($name, $value=null, $override=true)
    {
        $name_tmp = strtolower($name);
        foreach ($this->headers as $i => $tmp) {
            if ($name_tmp == strtolower($tmp[0])) {
                if ($override) {
                    $this->headers[$i][1] = $value;
                }
                return;
            }
        }
        $this->addHeader($name, $value);
    }

    /**
     * HTTPレスポンスヘッダをセットする
     * 
     * @param string ヘッダ名
     * @param string ヘッダ値
     */
    public function addHeader($name, $value=null)
    {
        $this->headers[] = array($name, $value);
    }

    /**
     * HTTPレスポンスヘッダを送信
     */
    public function sendHeader()
    {
        foreach ($this->headers as $tmp) {
            if ($tmp[1] !== null) {
                header($tmp[0] . ': ' . $tmp[1]);
            } else {
                header($tmp[0]);
            }
        }
    }

    /**
     * 表示情報を出力する
     * 
     * @param SyL_ViewAbstract 表示オブジェクト
     */
    public function display(SyL_ViewAbstract $view)
    {
        self::cleanBuffer();

        if ($view instanceof SyL_ViewNull) {
            return;
        }

        $content_type = $view->getContentType();
        $output       = $view->getRender();

        if ($content_type) {
            $this->setHeader('Content-Type', $content_type, false);
        }
        if ($this->gzip_compress > 0) {
            $this->compressGzip($output);
        }
        if ($this->enable_content_length) {
            $this->setHeader('Content-Length', strlen($output));
        }

        if (SYL_CACHE && ($this->response_cache_time > 0)) {
            $this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s T', time()));
            // ブラウザにキャッシュされないため Expires ヘッダを追加
            $this->setHeader('Expires', 'Thu, 01 Dec 1999 20:00:00 GMT');

            $serial_header      = serialize($this->headers);
            $serial_header_size = strlen($serial_header);
            SyL_CacheStorageAbstract::getInstance()->setResponseCache("{$serial_header_size}:{$serial_header}{$output}", $this->response_cache_time);
        }

        $this->sendHeader();
        echo $output;
    }

    /**
     * キャッシュ情報を出力する
     *
     * キャッシュ情報があれば、通信は終了する。
     */
    public function displayCache()
    {
        if (SYL_CACHE && ($this->response_cache_time > 0)) {
            if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $if_modified_since = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']);
                if ($if_modified_since + $this->response_cache_time >= time()) {
                    self::cleanBuffer();
                    SyL_Logger::info('304 Not Modified -> ' . $_SERVER['PHP_SELF']);
                    $this->setStatusHeader('304');
                    $this->sendHeader();
                    exit;
                }
            } else {
                if (isset($_SERVER['HTTP_CACHE_CONTROL']) && ($_SERVER['HTTP_CACHE_CONTROL'] == 'no-cache')) {
                    // Cache-Control == no-cache の場合は、レスポンスキャッシュを使用しない
                } else if (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'GET')) {
                    // GET のときのみキャッシュを行う
                    $output = SyL_CacheStorageAbstract::getInstance()->getResponseCache($this->response_cache_time);
                    if ($output !== null) {
                        self::cleanBuffer();
                        SyL_Logger::info('Response Cache OK -> ' . $_SERVER['PHP_SELF']);

                        $pos = strpos($output, ':');
                        $serial_header_size = substr($output, 0, $pos);
                        $this->headers = unserialize(substr($output, $pos + 1, $serial_header_size));
                        $this->sendHeader();
                        echo substr($output, $pos + 1 + $serial_header_size);
                        exit;
                    }
                }
            }
        }
    }

    /**
     * gzip圧縮を使用可能なら使用する
     * 
     * @param string 出力データ
     */
    private function compressGzip(&$data)
    {
        if (extension_loaded('zlib')) {
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && preg_match('/((x\-)?gzip)/', $_SERVER['HTTP_ACCEPT_ENCODING'], $matches)) {
                $this->addHeader('Content-Encoding', $matches[1]);
                $this->addHeader('Vary', 'Accept-Encoding');

                $size = strlen($data);
                $crc  = crc32($data);
                $data = gzcompress($data, $this->gzip_compress);
                $data = substr($data, 0, strlen($data) - 4);
                $data = "\x1f\x8b\x08\x00\x00\x00\x00\x00" . $data . pack('V', $crc) . pack('V', $size);
            }
        }
    }

    /**
     * 全バッファリングを破棄する
     */
    private static function cleanBuffer()
    {
        if (!headers_sent() && (SyL_Logger::getMode() < SYL_LOG_DEBUG)) {
            while (ob_get_level()) {
                ob_end_clean();
            }
        }
    }

    /**
     * ステータスコードからステータスヘッダを取得する
     * 
     * @param string ステータスコード
     * @param string HTTPバージョン
     * @return string ステータスヘッダ
     */
    private static function getStatusHeader($status_code, $http_version='1.1')
    {
        $message = '';
        switch ($status_code) {
        case '100': $message = 'Continue'; break;
        case '101': $message = 'Switching Protocols'; break;
        case '200': $message = 'OK'; break;
        case '201': $message = 'Created'; break;
        case '202': $message = 'Accepted'; break;
        case '203': $message = 'Non-Authoritative Information'; break;
        case '204': $message = 'No Content'; break;
        case '205': $message = 'Reset Content'; break;
        case '206': $message = 'Partial Content'; break;
        case '300': $message = 'Multiple Choices'; break;
        case '301': $message = 'Moved Permanently'; break;
        case '302': $message = 'Found'; break;
        case '303': $message = 'See Other'; break;
        case '304': $message = 'Not Modified'; break;
        case '305': $message = 'Use Proxy'; break;
        case '306': $message = '(Unused)'; break;
        case '307': $message = 'Temporary Redirect'; break;
        case '400': $message = 'Bad Request'; break;
        case '401': $message = 'Unauthorized'; break;
        case '402': $message = 'Payment Required'; break;
        case '403': $message = 'Forbidden'; break;
        case '404': $message = 'Not Found'; break;
        case '405': $message = 'Method Not Allowed'; break;
        case '406': $message = 'Not Acceptable'; break;
        case '407': $message = 'Proxy Authentication Required'; break;
        case '408': $message = 'Request Timeout'; break;
        case '409': $message = 'Conflict'; break;
        case '410': $message = 'Gone'; break;
        case '411': $message = 'Length Required'; break;
        case '412': $message = 'Precondition Failed'; break;
        case '413': $message = 'Request Entity Too Large'; break;
        case '414': $message = 'Request-URI Too Long'; break;
        case '415': $message = 'Unsupported Media Type'; break;
        case '416': $message = 'Requested Range Not Satisfiable'; break;
        case '417': $message = 'Expectation Failed'; break;
        case '500': $message = 'Internal Server Error'; break;
        case '501': $message = 'Not Implemented'; break;
        case '502': $message = 'Bad Gateway'; break;
        case '503': $message = 'Service Unavailable'; break;
        case '504': $message = 'Gateway Timeout'; break;
        case '505': $message = 'HTTP Version Not Supported'; break;
        default   : return false;
        }

        return "HTTP/{$http_version} {$status_code} {$message}";
    }
}
