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

/** HTTP認証クラス */
require_once 'SyL_HttpAuthenticationAbstract.php';

/**
 * ダイジェスト認証クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpAuthenticationDigest extends SyL_HttpAuthenticationAbstract
{
    /**
     * realm
     *
     * @var string
     */
    protected $realm = null;
    /**
     * nonce
     *
     * @var string
     */
    protected $nonce = null;
    /**
     * uri
     *
     * @var string
     */
    protected $uri = null;
    /**
     * algorithm
     *
     * @var string
     */
    protected $algorithm = 'MD5';
    /**
     * qop
     *
     * @var string
     */
    protected $qop = 'auth';
    /**
     * nc
     *
     * @var string
     */
    protected $nc = '00000001';
    /**
     * cnonce
     *
     * @var string
     */
    protected $cnonce = null;
    /**
     * HTTPメソッド
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * コンストラクタ
     * 
     * @param string 認証ID
     * @param string 認証パスワード
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->cnonce = md5(uniqid('SyL_', true));
    }

    /**
     * 認証ヘッダ値を取得する
     * 
     * @return string 認証ヘッダ値
     */
    public function getHeaderValue()
    {
        $value = null;
        if ($this->realm && $this->nonce) {
            $a1 = md5(sprintf('%s:%s:%s', $this->username, $this->realm , $this->password));
            $a2 = md5(sprintf('%s:%s', $this->method, $this->uri));
            $response = md5(sprintf('%s:%s:%s:%s:%s:%s', $a1, $this->nonce, $this->nc, $this->cnonce, $this->qop, $a2));
            $value = sprintf('Digest username="%s", realm="%s", nonce="%s", uri="%s", algorithm=%s, qop=%s, nc=%s, cnonce="%s", response="%s"', $this->username, $this->realm, $this->nonce, $this->uri, $this->algorithm, $this->qop, $this->nc, $this->cnonce, $response);
        }
        return $value;
    }

    /**
     * realmをセットする
     * 
     * @param string realm
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * nonceをセットする
     * 
     * @param string nonce
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * uriをセットする
     * 
     * @param string uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * algorithmをセットする
     * 
     * @param string algorithm
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * qopをセットする
     * 
     * @param string qop
     */
    public function setQop($qop)
    {
        $this->qop = $qop;
    }

    /**
     * ncをセットする
     * 
     * @param string nc
     */
    public function setNc($nc)
    {
        $this->nc = $nc;
    }

    /**
     * cnonceをセットする
     * 
     * @param string cnonce
     */
    public function setCnonce($cnonce)
    {
        $this->cnonce = $cnonce;
    }

    /**
     * HTTPメソッドをセットする
     * 
     * @param string HTTPメソッド
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }
}
