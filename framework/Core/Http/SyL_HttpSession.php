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
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** セッション例外クラス */
require_once 'SyL_HttpSessionException.php';

/**
 * セッションクラス
 * 
 * @package    SyL.Core
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpSession
{
    /**
     * コンストラクタ
     *
     * @param string セッション保存先
     * @param string セッションハンドラ名
     * @throws SyL_HttpSessionStartedException セッション開始済みの場合
     */
    public function start()
    {
        // ex1)
        // session.save_handler = sqlite
        // session.save_path = /path/to/sqlite
        // ex2)
        // session.save_handler = memcache
        // session.save_path = tcp://hostname:port?...
        // ...
        if ($session_save_handler) {
            ini_set('session.save_handler', $session_save_handler);
        }
        if ($session_save_path) {
            ini_set('session.save_path', $session_save_path);
        }
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        if (isset($_SESSION)) {
            session_write_close();
        }
    }

    /**
     * セッションを開始する
     *
     * @param string セッション名
     * @throws SyL_HttpSessionStartedException セッション開始済みの場合
     */
    public function start($session_name='')
    {
        if (isset($_SESSION)) {
            throw new SyL_HttpSessionStartedException('already session started');
        }

        if ($session_name) {
            session_name($session_name);
        }

        session_start();
    }

    /**
     * セッションキャッシュリミッタをセットする。
     *
     * セッションを開始する前に（start メソッドを実行する前に）コールする必要がある
     *
     * @param string キャッシュリミッタ
     * @param int キャッシュの有効期限
     * @throws SyL_HttpSessionStartedException セッション開始済みの場合
     * @throws SyL_InvalidParameterException 指定パラメータ以外の場合
     */
    public function setCacheLimiter($limter, $expire=null)
    {
        if (isset($_SESSION)) {
            throw new SyL_HttpSessionStartedException('already session started');
        }

        switch ($limter) {
        case 'public':
        case 'private_no_expire':
        case 'private':
        case 'nocache':
            break;
        default:
            throw new SyL_InvalidParameterException("invalid session_cache_limiter parameter ({$limter})");
        }

        if ($expire !== null) {
            session_cache_expire($expire);
        }
        session_cache_limiter($limter);
    }

    /**
     * セッションパラメータをセットする。
     *
     * セッションを開始する前に（start メソッドを実行する前に）コールする必要がある
     *
     * @param string セッション有効パス
     * @param string セッション有効ドメイン
     * @param bool セキュア な接続の場合にのみ
     * @param bool httponly フラグの送信
     * @throws SyL_HttpSessionStartedException セッション開始済みの場合
     */
    public function setCookieParams($path='/', $domain='', $secure=false, $httponly=false)
    {
        if (isset($_SESSION)) {
            throw new SyL_HttpSessionStartedException('already session started');
        }
        session_set_cookie_params(0, $path, $domain, $secure, $httponly);
    }

    /**
     * セッションパラメータを取得する
     * 
     * @param string パラメータ名
     * @return mixed セッション値
     * @throws SyL_HttpSessionNotStartedException セッションが開始されていない場合
     */
    public function get($name)
    {
        if (!isset($_SESSION)) {
            throw new SyL_HttpSessionNotStartedException('session not started');
        }
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * セッションにパラメータをセットする
     * 
     * @param string パラメータ名
     * @param mixed セッション値
     * @throws SyL_HttpSessionNotStartedException セッションが開始されていない場合
     */
    public function set($name, $value)
    {
        if (!isset($_SESSION)) {
            throw new SyL_HttpSessionNotStartedException('session not started');
        }
        $_SESSION[$name] = $value;
    }

    /**
     * セッションパラメータを削除する
     * 
     * @param string パラメータ名
     * @throws SyL_HttpSessionNotStartedException セッションが開始されていない場合
     */
    public function remove($name)
    {
        if (!isset($_SESSION)) {
            throw new SyL_HttpSessionNotStartedException('session not started');
        }
        unset($_SESSION[$name]);
    }

    /**
     * 全てのセッションパラメータを削除して、セッションを終了する
     */
    public function removeClose()
    {
        if (isset($_COOKIE[session_name()])) {
           setcookie(session_name(), '', time()-3600);
        }
        session_destroy();
    }

    /**
     * セッションを終了する
     */
    public function close()
    {
        session_write_close();

        // 2回目以降のsession_start()でクッキーを発行しない対応
        if (ini_get('session.use_cookies')) {
          ini_set('session.use_cookies', '0');
        }
    }

    /**
     * セッションIDを変更する
     *
     * @throws SyL_HttpSessionNotStartedException セッションが開始されていない場合
     */
    public function regenerateId()
    {
        if (!isset($_SESSION)) {
            throw new SyL_HttpSessionNotStartedException('session not started');
        }
        session_regenerate_id(true);
    }

    /**
     * セッション名を取得する
     * 
     * @return string セッション名
     */
    public function getSessionName()
    {
        return session_name();
    }

    /**
     * セッションIDを取得する
     * 
     * @return string セッションID
     */
    public function getSessionId()
    {
        return session_id();
    }

    /**
     * パラメータ名付セッションIDを取得する
     * 
     * @return string パラメータ名付セッションID
     */
    public function getSid()
    {
        return SID;
    }
}
