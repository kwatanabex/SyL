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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** HTTPクライアントクラス */
require_once dirname(__FILE__) . '/../Http/SyL_HttpClient.php';
/** OAuthコンシューマクラス */
require_once 'SyL_OAuthConsumer.php';
/** OAuthリクエストクラス */
require_once 'SyL_OAuthClientRequest.php';

/**
 * OAuthクライアントクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.OAuth
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_OAuthClient extends SyL_HttpClient
{
    /**
     * リクエストオブジェクトを取得する
     * 
     * @param string リクエストURL
     * @param string リクエストメソッド
     * @param string HTTPバージョン
     */
    public function createRequest($url='/', $method='GET', $version='1.1')
    {
        $request = new SyL_OAuthClientRequest($this, $url, $method, $version);
        if ((float)$version >= 1.1) {
            $request->setHost($this->getHost());
        }
        return $request;
    }

    /**
     * RequestTokenリクエストを行い、OAuthレスポンスオブジェクトを取得する
     * 
     * @param SyL_OAuthClientRequest OAuthリクエストオブジェクト
     * @return SyL_OAuthClientResponseRequestToken OAuthレスポンスオブジェクト
     */
    public function getRequestToken(SyL_OAuthClientRequest $request)
    {
        $request->createOAuthRequestTokenParameter();
        include_once 'SyL_OAuthClientResponseRequestToken.php';
        return new SyL_OAuthClientResponseRequestToken(parent::sendRequest($request));
    }

    /**
     * End Userの認可要求を行い、OAuthレスポンスオブジェクトを取得する
     * 
     * @param SyL_OAuthClientRequest OAuthリクエストオブジェクト
     * @return SyL_OAuthClientResponseRequestAuth OAuthレスポンスオブジェクト
     */
    public function getRequestAuth(SyL_OAuthClientRequest $request)
    {
        $request->createOAuthRequestAuthParameter();
        include_once 'SyL_OAuthClientResponseRequestAuth.php';
        return new SyL_OAuthClientResponseRequestAuth(parent::sendRequest($request));
    }

    /**
     * Access Tokenリクエストを行い、OAuthレスポンスオブジェクトを取得する
     * 
     * @param SyL_OAuthClientRequest OAuthリクエストオブジェクト
     * @return SyL_OAuthClientResponseAccessToken OAuthレスポンスオブジェクト
     */
    public function getAccessToken(SyL_OAuthClientRequest $request)
    {
        $request->createOAuthAccessTokenParameter();
        include_once 'SyL_OAuthClientResponseAccessToken.php';
        return new SyL_OAuthClientResponseAccessToken(parent::sendRequest($request));
    }

    /**
     * OAuth対応リクエストを送信して、レスポンスオブジェクトを取得する
     * 
     * @param SyL_OAuthClientRequest OAuthリクエストオブジェクト
     * @return SyL_HttpClientResponse レスポンスオブジェクト
     * @throws SyL_InvalidClassException パラメータが SyL_OAuthClientRequest クラスでない場合
     */
    public function sendRequest(SyL_HttpClientRequest $request)
    {
        if (!($request instanceof SyL_OAuthClientRequest)) {
            throw new SyL_InvalidClassException("invalid method parameter. require `SyL_OAuthClientRequest'");
        }
        $request->createOAuthRequestHeader();
        return parent::sendRequest($request);
    }
}
