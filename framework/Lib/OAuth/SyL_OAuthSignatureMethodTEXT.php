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
 * @subpackage SyL.Lib.OAuth
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** OAuthシグニチャメソッドクラス */
require_once 'SyL_OAuthSignatureMethodAbstract.php';

/**
 * OAuth TEXTシグニチャメソッドクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.OAuth
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_OAuthSignatureMethodTEXT extends SyL_OAuthSignatureMethodAbstract
{
    /**
     * シグニチャメソッド名
     * 
     * @var string
     */
    protected $method_name = 'PLAINTEXT';

    /**
     * シグニチャを生成する
     * 
     * @return string シグニチャ
     */
    public function create(SyL_OAuthClientRequest $request)
    {
        $consumer_secret = $request->getConsumerSecret();
        $consumer_secret = SyL_UtilConverter::encodeUrlToRfc3986($consumer_secret);
        $token_secret    = $request->getTokenSecret();
        $token_secret    = SyL_UtilConverter::encodeUrlToRfc3986($token_secret);

        return $consumer_secret . '&' . $token_secret;
    }
}
