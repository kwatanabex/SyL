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

/**
 * OAuthリクエストクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.OAuth
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_OAuthClientRequest extends SyL_HttpClientRequest
{
    /**
     * コンシューマオブジェクト
     * 
     * @var SyL_OAuthConsumer
     */
    private $consumer = null;
    /**
     * シグニチャメソッドオブジェクト
     * 
     * @var string
     */
    private $signature_method = null;
    /**
     * Consumerへの戻り先URL
     * 
     * @var string
     */
    private $callback = 'oob';
    /**
     * OAuth Verfier
     * 
     * @var string
     */
    private $verifier = null;
    /**
     * OAuthクライアントオブジェクト
     * 
     * @var SyL_OAuthClient
     */
    private $client = null;

    /**
     * コンストラクタ
     * 
     * @param SyL_OAuthClient OAuthクライアントオブジェクト
     * @param string HTTPリクエスト対象URL
     * @param string HTTPリクエストメソッド
     * @param string HTTPバージョン
     */
    public function __construct(SyL_OAuthClient $client, $url, $method='GET', $version='1.1')
    {
        parent::__construct($url, $method, $version);
        $this->client = $client;
    }

    /**
     * OAuthクライアントオブジェクトを取得する
     * 
     * @return SyL_OAuthClient OAuthクライアントオブジェクト
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * コンシューマ情報をセットする
     * 
     * @param SyL_OAuthConsumer コンシューマオブジェクト
     * @param string Consumerへの戻り先URL
     */
    public function setConsumer(SyL_OAuthConsumer $consumer, $callback=null)
    {
        $this->consumer = $consumer;
        $this->callback = $callback;
    }

    /**
     * コンシューマオブジェクトを取得する
     * 
     * @return SyL_OAuthConsumer コンシューマオブジェクト
     */
    public function getConsumer()
    {
        return $this->consumer;
    }

    /**
     * OAuth Verfierをセットする
     * 
     * @param string OAuth Verfier
     */
    public function setVerifier($verifier)
    {
        $this->verifier = $verifier;
    }

    /**
     * シグニチャメソッドオブジェクトをセットする
     * 
     * @param SyL_OAuthSignatureMethodAbstract シグニチャメソッドオブジェクト
     */
    public function setSignatureMethod(SyL_OAuthSignatureMethodAbstract $signature_method)
    {
        $this->signature_method = $signature_method;
    }

    /**
     * Request Tokenを取得するためのパラメータを作成する
     */
    public function createOAuthRequestTokenParameter()
    {
        if (!($this->signature_method instanceof SyL_OAuthSignatureMethodAbstract)) {
            throw new SyL_InvalidParameterException('signature method object not found');
        }

        $this->setParameter('oauth_consumer_key', $this->consumer->consumer_key);
        $this->setParameter('oauth_signature_method', $this->signature_method->getMethodName());
        $this->setParameter('oauth_timestamp', time());
        $this->setParameter('oauth_nonce', sha1(uniqid('SyL', true)));
        $this->setParameter('oauth_version', '1.0');
        $this->setParameter('oauth_callback', $this->callback);
        $this->setParameter('oauth_signature', $this->signature_method->create($this));
    }

    /**
     * End Userの認可要求を行うためのパラメータを作成する
     */
    public function createOAuthRequestAuthParameter()
    {
        $this->setParameter('oauth_token', $this->consumer->token);
    }

    /**
     * Access Tokenを取得するためのパラメータを作成する
     */
    public function createOAuthAccessTokenParameter()
    {
        if (!($this->signature_method instanceof SyL_OAuthSignatureMethodAbstract)) {
            throw new SyL_InvalidParameterException('signature method object not found');
        }

        $this->setParameter('oauth_consumer_key', $this->consumer->consumer_key);
        $this->setParameter('oauth_token', $this->consumer->token);
        $this->setParameter('oauth_signature_method', $this->signature_method->getMethodName());
        $this->setParameter('oauth_timestamp', time());
        $this->setParameter('oauth_nonce', sha1(uniqid('SyL', true)));
        $this->setParameter('oauth_verifier', $this->verifier);
        $this->setParameter('oauth_version', '1.0');
        $this->setParameter('oauth_signature', $this->signature_method->create($this));

        parent::getSource();
    }

    /**
     * OAuth リクエストを行うためのヘッダを作成する
     */
    public function createOAuthRequestHeader()
    {
        if (!($this->signature_method instanceof SyL_OAuthSignatureMethodAbstract)) {
            throw new SyL_InvalidParameterException('signature method object not found');
        }

        $this->setParameter('oauth_consumer_key', $this->consumer->consumer_key);
        $this->setParameter('oauth_token', $this->consumer->token);
        $this->setParameter('oauth_signature_method', $this->signature_method->getMethodName());
        $this->setParameter('oauth_timestamp', time());
        $this->setParameter('oauth_nonce', sha1(uniqid('SyL', true)));
        $this->setParameter('oauth_version', '1.0');
        $this->setParameter('oauth_signature', $this->signature_method->create($this));

        $auth_parameters = array();
        $auth_parameters['oauth_consumer_key']     = $this->getParameter('oauth_consumer_key');
        $auth_parameters['oauth_nonce']            = $this->getParameter('oauth_nonce');
        $auth_parameters['oauth_signature_method'] = $this->getParameter('oauth_signature_method');
        $auth_parameters['oauth_timestamp']        = $this->getParameter('oauth_timestamp');
        $auth_parameters['oauth_token']            = $this->getParameter('oauth_token');
        $auth_parameters['oauth_version']          = $this->getParameter('oauth_version');
        $auth_parameters['oauth_signature']        = $this->getParameter('oauth_signature');

        $auth_parameter = '';
        foreach ($auth_parameters as $name => $values) {
            if ($auth_parameter) {
                $auth_parameter .= ', ';
            }
            $auth_parameter .= $name . '="' . SyL_UtilConverter::encodeUrlToRfc3986($values[0]) . '"';
            $this->removeParameter($name);
        }
        $this->setHeader('Authorization', 'OAuth ' . $auth_parameter);
    }
}
