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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * クッキークラス
 * 
 * @package    SyL.Core
 * @subpackage SyL.Core.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpCookie
{
    /**
     * 有効期限
     *
     * @var mixed
     */
     private $expire = 0;
    /**
     * パス
     *
     * @var string
     */
     private $path = '';
    /**
     * ドメイン
     *
     * @var string
     */
     private $domain = '';
    /**
     * HTTPS
     *
     * @var bool
     */
     private $secure = false;
    /**
     * HTTP only
     *
     * @var bool
     */
     private $httponly = false;
    /**
     * 送信済みクッキー配列
     *
     * @var array
     */
     private $sended_cookies = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
    }

    /**
     * クッキー送信時に使用する有効期限をセットする
     *
     * @param int 有効期限
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;
    }

    /**
     * クッキーパラメータをセットする
     *
     * @param string クッキー有効パス
     * @param string クッキー有効ドメイン
     * @param bool セキュアな接続の場合にのみ
     * @param bool httponly フラグの送信
     */
    public function setCookieParams($path='/', $domain='', $secure=false, $httponly=false)
    {
        $this->path     = $path;
        $this->domain   = $domain;
        $this->secure   = $secure;
        $this->httponly = $httponly;
    }

    /**
     * クッキーを取得する
     * 
     * @param string パラメータ名
     * @return string クッキー値
     */
    public function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

    /**
     * 全てのクッキーを取得する
     * 
     * @return array クッキー配列
     */
    public function gets()
    {
        return $_COOKIE;
    }

    /**
     * パラメータをクッキーにセットする
     * 
     * @param string パラメータ名
     * @param string パラメータ値
     * @param int クッキーの有効期限
     * @param string クッキーを有効としたいパス
     * @param string クッキーが有効なドメイン
     * @param bool HTTPS 接続の場合にのみクッキー送信フラグ
     * @param bool HTTP を通してのみクッキーにアクセス可能なフラグ
     */
    public function set($name, $value, $expire=null, $path=null, $domain=null, $secure=null, $httponly=null)
    {
        if ($expire === null) {
            $expire = $this->expire;
        }
        if ($path === null) {
            $path = $this->path;
        }
        if ($domain === null) {
            $domain = $this->domain;
        }
        if ($secure === null) {
            $secure = $this->secure;
        }
        if ($httponly === null) {
            $httponly = $this->httponly;
        }

        // 単位変換 y: 年、m: 月、d: 日、h: 時、i: 分、s: 秒
        if (preg_match('/^(\d+)([ymdhis])$/', $expire, $match)) {
            $strtotime_format = '';
            switch ($match[2]) {
            case 'y': $strtotime_format = '+1 year';   break;
            case 'm': $strtotime_format = '+1 month';  break;
            case 'd': $strtotime_format = '+1 day';    break;
            case 'h': $strtotime_format = '+1 hour';   break;
            case 'i': $strtotime_format = '+1 minute'; break;
            case 's': $strtotime_format = '+1 second'; break;
            }
            if ($strtotime_format) {
                $expire = $match[1] * (strtotime($strtotime_format) - time());
            }
        }
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);

        $this->sended_cookies[$name] = array($path, $domain, $secure, $httponly);
    }

    /**
     * 複数のパラメータをクッキーにセットする
     * 
     * @param array パラメータ配列
     * @param int クッキーの有効期限
     * @param string クッキーを有効としたいパス
     * @param string クッキーが有効なドメイン
     * @param bool HTTPS 接続の場合にのみクッキー送信フラグ
     * @param bool HTTP を通してのみクッキーにアクセス可能なフラグ
     */
    public function sets($values, $expire=null, $path=null, $domain=null, $secure=null, $httponly=null)
    {
        foreach ($values as $name => $value) {
            $this->set($name, $value, $expire, $path, $domain, $secure, $httponly);
        }
    }

    /**
     * クッキーを削除する
     * 
     * @param string パラメータ名
     * @param string クッキーを有効としたいパス
     * @param string クッキーが有効なドメイン
     * @param bool HTTPS 接続の場合にのみクッキー送信フラグ
     * @param bool HTTP を通してのみクッキーにアクセス可能なフラグ
     */
    public function remove($name, $path=null, $domain=null, $secure=null, $httponly=null)
    {
        $this->set($name, '', time()-3600, $path, $domain, $secure, $httponly);
    }

    /**
     * 認識済みのクッキーを全て削除する
     * 
     * @param string クッキーを有効としたいパス
     * @param string クッキーが有効なドメイン
     * @param bool HTTPS 接続の場合にのみクッキー送信フラグ
     * @param bool HTTP を通してのみクッキーにアクセス可能なフラグ
     */
    public function deletes($path=null, $domain=null, $secure=null, $httponly=null)
    {
        foreach ($this->sended_cookies as $name => $values) {
            $this->remove($name, $values[0], $values[1], $values[2], $values[3]);
        }
        foreach ($_COOKIE as $name => $values) {
            $this->remove($name, $path, $domain, $secure, $httponly);
        }
    }
}
