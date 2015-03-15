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
 * @subpackage SyL.Core.Request
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * WEBリスエストクラス
 *
 * @package    SyL.Core
 * @subpackage SyL.Core.Request
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright  2006-2010 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RequestWeb extends SyL_RequestAbstract
{
    /**
     * クッキーのデフォルト有効期限
     *
     * @var mixed
     */
    protected $cookie_expire = 0;
    /**
     * クッキー／セッションのデフォルトパス
     *
     * @var string
     */
    protected $cookie_path = '/';
    /**
     * クッキー／セッションのデフォルトドメイン
     *
     * @var string
     */
    protected $cookie_domain = '';
    /**
     * クッキー／セッションのデフォルトHTTPS
     *
     * @var bool
     */
    protected $cookie_secure = false;
    /**
     * クッキー／セッションのデフォルトHTTP only
     *
     * @var bool
     */
    protected $cookie_httponly = false;

    /**
     * セッション名
     *
     * @var string
     */
    protected $session_name = 'sylid';
    /**
     * セッション保存先
     *
     * @var string
     */
    protected $session_save_path = '';
    /**
     * セッションハンドラ名
     *
     * @var string
     */
    protected $session_save_handler = '';
    /**
     * セッションキャッシュリミッタ
     *
     * @var string
     */
    protected $session_cache_limiter = 'nocache';
    /**
     * リクエストごとにセッションIDを変更するフラグ
     *
     * @var bool
     */
    protected $session_auto_regenarate = false;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
        parent::__construct();

        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
        $addr   = isset($_SERVER['REMOTE_ADDR'])    ? $_SERVER['REMOTE_ADDR']    : '';
        $uri    = isset($_SERVER['REQUEST_URI'])    ? $_SERVER['REQUEST_URI']    : '';
        SyL_Logger::info("request: \"{$method} {$uri}\" - {$addr}");
    }

    /**
     * 外部パラメータを取得する
     *
     * @return array 外部パラメータ
     */
    protected function getInputs()
    {
        return ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST + $_GET : $_GET + $_POST;
    }

    /**
     * GETリクエストか判定する
     *
     * @return bool true: GETリクエスト、false: GETリクエスト以外
     */
    public function isGet()
    {
        return (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'GET'));
    }

    /**
     * POSTリクエストか判定する
     *
     * @return bool true: POSTリクエスト、false: POSTリクエスト以外
     */
    public function isPost()
    {
        return (isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST'));
    }

    /**
     * サーバー名を取得する
     *
     * @return string サーバー名
     */
    public function getServerName()
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        } else if (isset($_SERVER['SERVER_NAME'])) {
            if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] != '80')) {
                return $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];
            } else {
                return $_SERVER['SERVER_NAME'];
            }
        } else {
            return null;
        }
    }

    /**
     * リクエストされたURLを取得する
     * クエリパラメータは含まない
     *
     * @return string リクエストされたURL
     */
    public function getUrlSelf()
    {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
        if (!$request_uri) {
            $request_uri = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : null;
            if (!$request_uri) {
                return null;
            }
        }
        $pos = strpos($request_uri, '?');
        if ($pos !== false) {
            $request_uri = substr($request_uri, 0, $pos);
        }

        if (substr($request_uri, -1) == '/') {
            return substr($request_uri, 0, -1);
        } else {
            return $request_uri;
        }
    }

    /**
     * SyLフレームワークのベースURLを取得する
     *
     * @return string SyLフレームワークのベースURL
     */
    public function getUrlBase()
    {
        $request_uri = $this->getUrlSelf();
        if (!$request_uri) {
            return $request_uri;
        }
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : null;
        if ($path_info) {
            if (preg_match ('/(.*)' . preg_quote($path_info, '/') . '$/', $request_uri, $matches)) {
                return isset($matches[1]) ? $matches[1] : '';
            } else if (preg_match ('/(.*)' . preg_quote($path_info, '/') . '$/', $request_uri . '/', $matches)) {
                return isset($matches[1]) ? $matches[1] : '';
            }
        }

        return $request_uri;
    }

    /**
     * クッキーオブジェクトを取得する
     *
     * @return SyL_HttpCookie クッキーオブジェクト
     */
    public function getCookieInstance()
    {
        static $cookie = null;
        if ($cookie == null) {
            include_once SYL_FRAMEWORK_DIR . '/Core/Http/SyL_HttpCookie.php';
            $cookie = new SyL_HttpCookie();
            $cookie->setExpire($this->cookie_expire);
            $cookie->setCookieParams($this->cookie_path, $this->cookie_domain, $this->cookie_secure, $this->cookie_httponly);
        }
        return $cookie;
    }

    /**
     * アップロードファイルオブジェクトを取得する
     *
     * @return SyL_HttpFileUpload アップロードファイルオブジェクト
     */
    public function getFileUploadInstance()
    {
        static $fileupload = null;
        if ($fileupload == null) {
            include_once SYL_FRAMEWORK_DIR . '/Core/Http/SyL_HttpFileUpload.php';
            $fileupload = new SyL_HttpFileUpload();
        }
        return $fileupload;
    }

    /**
     * クッキー値をセットする
     * 
     * @param string パラメータ名
     * @param string パラメータ値
     */
    public function setCookie($name, $value)
    {
        $this->getCookieInstance()->set($name, $value);
    }

    /**
     * クッキー値を取得する
     *
     * @param string パラメータ名
     * @return string パラメータ値
     */
    public function getCookie($name)
    {
        return $this->getCookieInstance()->get($name);
    }

    /**
     * アップロードされたサーバー上のファイル名を取得する
     *
     * @param string アップロード要素名
     * @return array アップロードされたサーバー上のファイル名の配列
     */
    public function getFileName($name)
    {
        return $this->getFileUploadInstance()->getFileName($name);
    }

}
