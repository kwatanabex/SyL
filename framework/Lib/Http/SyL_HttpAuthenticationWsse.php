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
 * WSSE認証クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_HttpAuthenticationWsse extends SyL_HttpAuthenticationAbstract
{
    /**
     * トークン
     *
     * @var string
     */
    private $nonce = '';
    /**
     * 作成日時
     *
     * @var string
     */
    private $created = '';
    /**
     * 認証ヘッダ
     *
     * @var string
     */
    protected $header_name = 'X-WSSE';

    /**
     * コンストラクタ
     * 
     * @param string 認証ID
     * @param string 認証パスワード
     */
    public function __construct($username, $password)
    {
        parent::__construct($username, $password);
        $this->created = date('Y-m-d\TH:i:s\Z');
        $this->nonce   = pack('H*', sha1(md5(time())));
    }

    /**
     * トークンをセットする
     * 
     * @param string トークン
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    /**
     * 作成日時をセットする
     * 
     * @param string トークン
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * 認証ヘッダ値を取得する
     * 
     * @return string 認証ヘッダ値
     */
    public function getHeaderValue()
    {
        $passwd_digest = $this->nonce . $this->created . $this->password;
        $passwd_digest = base64_encode(pack('H*', sha1($this->nonce . $this->created . $this->password)));
        return sprintf('UsernameToken Username="%s", PasswordDigest="%s", Created="%s", Nonce="%s"', $this->username, $passwd_digest, $this->created, base64_encode($this->nonce));
    }
}
