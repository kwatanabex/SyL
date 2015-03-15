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
 * @subpackage SyL.Core.Session
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** セッション例外クラス */
require_once 'SyL_SessionException.php';

/**
 * セッションクラス
 * 
 * @package    SyL.Core
 * @subpackage SyL.Core.Session
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_SessionAbstract
{
    /**
     * セッション名
     *
     * @var string
     */
    protected $name = 'sylid';
    /**
     * セッションハンドラ名
     *
     * @var string
     */
    protected $save_handler = null;
    /**
     * セッション保存先
     *
     * @var string
     */
    protected $save_path = null;
    /**
     * セッションキャッシュリミッタ名
     *
     * @var string
     */
    protected $cache_limiter = null;
    /**
     * セッションの有効期限
     *
     * @var int
     */
    protected $cache_expire = null;
    /**
     * クッキーパス
     *
     * @var string
     */
    protected $cookie_path = '/';
    /**
     * クッキードメイン
     *
     * @var string
     */
    protected $cookie_domain = null;
    /**
     * セキュアな接続 Only
     *
     * @var bool
     */
    protected $cookie_secure_only = false;
    /**
     * HTTP Only
     *
     * @var bool
     */
    protected $cookie_http_only = false;

    /**
     * コンストラクタ
     */
    protected function __construct()
    {
    }

    /**
     * セッションオブジェクトを取得する
     *
     * @return SyL_SessionAbstract セッションオブジェクト
     */
    public static function getInstance()
    {
        static $session = null;
        if (!self::isStartedSession()) {
            $session = null;
            if (SYL_APP_TYPE != SyL_AppType::WEB) {
                throw new SyL_InvalidOperationException('invalid env type (' . SYL_APP_TYPE . ')');
            }

            $name = SyL_CustomClass::getSessionClass();
            if ($name) {
                $classname = SyL_Loader::userLib($name);
            } else {
                $classname = 'SyL_SessionDefault';
                include_once $classname . '.php';
            }
            if (!is_subclass_of($classname, __CLASS__)) {
                throw new SyL_InvalidClassException("invalid session class `{$classname}'. not extends `" . __CLASS__ . "' class");
            }
            $session = new $classname();
            $session->startBefore();
            SyL_Logger::trace('session start');
            session_start();
            SyL_Logger::trace('session parameters: ' . print_r($_SESSION, true));
            $session->startAfter();
        }
        return $session;
    }

    /**
     * セッションが開始されているか判定する
     *
     * @return bool セッションが開始されているか
     */
    protected static function isStartedSession()
    {
        return isset($_SESSION);
    }

    /**
     * セッションを開始直前の処理
     */
    protected function startBefore()
    {
        // ex1)
        // session.save_handler = sqlite
        // session.save_path = /path/to/sqlite
        // ex2)
        // session.save_handler = memcache
        // session.save_path = tcp://hostname:port?...
        // ...
        if (!$this->save_handler || ($this->save_handler == 'files')) {
            if (!$this->save_path) {
                $save_path = SYL_PROJECT_DIR . '/var/session';
                if (is_writable($save_path)) {
                    $this->save_path = $save_path;
                }
            }
        }
        
        if ($this->save_handler) {
            ini_set('session.save_handler', $this->save_handler);
        }
        if ($this->save_path) {
            ini_set('session.save_path', $this->save_path);
        }

        session_name($this->name);

        if ($this->cache_limiter) {
            switch ($this->cache_limiter) {
            case 'public':
            case 'private_no_expire':
            case 'private':
            case 'nocache':
                break;
            default:
                throw new SyL_InvalidParameterException("invalid session_cache_limiter parameter ({$this->cache_limiter})");
            }
            session_cache_limiter($this->cache_limiter);
        }

        if ($this->cache_expire !== null) {
            session_cache_expire($this->cache_expire);
        }

        session_set_cookie_params(0, $this->cookie_path, $this->cookie_domain, $this->cookie_secure_only, $this->cookie_http_only);
    }

    /**
     * セッションを開始直後の処理
     */
    protected function startAfter()
    {
    }

    /**
     * セッションパラメータを取得する
     * 
     * @param string パラメータ名
     * @return mixed セッション値
     * @throws SyL_SessionNotStartedException セッションが開始されていない場合
     */
    public function get($name)
    {
        if (!self::isStartedSession()) {
            throw new SyL_SessionNotStartedException('session not started');
        }
        return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
    }

    /**
     * セッションにパラメータをセットする
     * 
     * @param string パラメータ名
     * @param mixed セッション値
     * @throws SyL_SessionNotStartedException セッションが開始されていない場合
     */
    public function set($name, $value)
    {
        if (!self::isStartedSession()) {
            throw new SyL_SessionNotStartedException('session not started');
        }
        $_SESSION[$name] = $value;
    }

    /**
     * セッションにパラメータがセットされているか確認する
     * 
     * @param string パラメータ名
     * @return bool セッションにパラメータがセットされているか
     * @throws SyL_SessionNotStartedException セッションが開始されていない場合
     */
    public function is($name)
    {
        if (!self::isStartedSession()) {
            throw new SyL_SessionNotStartedException('session not started');
        }
        return array_key_exists($name, $_SESSION);
    }

    /**
     * セッションパラメータを削除する
     * 
     * @param string パラメータ名
     * @throws SyL_SessionNotStartedException セッションが開始されていない場合
     */
    public function remove($name)
    {
        if (!self::isStartedSession()) {
            throw new SyL_SessionNotStartedException('session not started');
        }
        unset($_SESSION[$name]);
    }

    /**
     * セッションを終了する
     *
     * @param bool セッション破棄フラグ
     */
    public function close($destroy=false)
    {
        if ($destroy) {
            SyL_Logger::trace('session destory');
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                $p = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
            }
            session_destroy();
        } else {
            SyL_Logger::trace('session close');
            session_write_close();
            // 2回目以降のsession_start()でクッキーを発行しない対応
            if (ini_get('session.use_cookies')) {
              ini_set('session.use_cookies', '0');
            }
        }
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
