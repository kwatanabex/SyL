<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Library
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
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** WEBサービスの例外クラス */
require_once 'SyL_WebServiceException.php';
/** WEBサービスリクエストクラス */
require_once 'SyL_WebServiceRequestAbstract.php';
/** WEBサービスレスポンスクラス */
require_once 'SyL_WebServiceResponseAbstract.php';
/** WEBサービス結果レコードクラス */
require_once 'SyL_WebServiceResultAbstract.php';
/** HTTPクライアントクラス */
require_once dirname(__FILE__) . '/../Http/SyL_HttpClient.php';

/**
 * WEBサービスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceClient
{
    /**
     * API提供ドメイン名
     *
     * @var string
     */
    private $domain = '';
    /**
     * API提供ドメインのディレクトリ
     *
     * @var string
     */
    private $domain_dir = '';
    /**
     * OAuth用パラメータ
     *
     * @var array
     */
    private $oauth_parameters = array(
        'consumer_key'       => null,
        'consumer_secret'    => null,
        'oauth_token'        => null,
        'oauth_token_secret' => null
    );

    /**
     * コンストラクタ
     *
     * @param string WEBサービスを提供するドメイン
     */
    public function __construct($domain)
    {
        $domain = str_replace('.', '_', $domain);
        $domain = ucfirst(strtolower($domain));

        $this->domain = $domain;
        $this->domain_dir = dirname(__FILE__) . '/' . $domain;
        if (!is_dir($this->domain_dir)) {
            throw new SyL_WebServiceNotFoundException("WebService not implemented in SyL Framework (domain directory not found: `{$this->domain_dir}')");
        }

        $base_file = $this->domain_dir . '/SyL_WebService' . $this->domain . 'Request.php';
        if (!is_file($base_file)) {
            throw new SyL_WebServiceNotFoundException("WebService not implemented in SyL Framework (api request base file not found: `{$base_file}')");
        }
        include_once $base_file;

        $base_file = $this->domain_dir . '/SyL_WebService' . $this->domain . 'Response.php';
        if (!is_file($base_file)) {
            throw new SyL_WebServiceNotFoundException("WebService not implemented in SyL Framework (api response base file not found: `{$base_file}')");
        }
        include_once $base_file;
    }

    /**
     * OAuth用コンシューマ情報をセットする
     * 
     * @param string コンシューマキー
     * @param string コンシューマシークレット
     */
    public function setConsumer($consumer_key, $consumer_secret)
    {
        $this->oauth_parameters['consumer_key']    = $consumer_key;
        $this->oauth_parameters['consumer_secret'] = $consumer_secret;
    }

    /**
     * OAuth用トークン情報をセットする
     * 
     * @param string トークン
     * @param string トークンシークレット
     */
    public function setToken($oauth_token, $oauth_token_secret)
    {
        $this->oauth_parameters['oauth_token']        = $oauth_token;
        $this->oauth_parameters['oauth_token_secret'] = $oauth_token_secret;
    }

    /**
     * OAuth Request Token を取得する
     * 
     * @param string コールバックURL
     */
    public function getRequestToken($callback)
    {
        $request = $this->createOAuthRequest();

        $ssl     = $request->isSsl();
        $host    = $request->getRequestHost();
        $port    = $request->getRequestPort();
        $timeout = $request->getTimeout();
        $method  = $request->getRequestMethod();
        $path    = $request->getRequestTokenPath();
        $response_class = $request->getResponseClass();

        include_once dirname(__FILE__) . '/../OAuth/SyL_OAuthClient.php';

        $signature_method = null;
        switch ($request->getSignatureMethod()) {
        case 'TEXT':
            include_once dirname(__FILE__) . '/../OAuth/SyL_OAuthSignatureMethodTEXT.php';
            $signature_method = new SyL_OAuthSignatureMethodTEXT();
            break;
        case 'HMACSHA1':
            include_once dirname(__FILE__) . '/../OAuth/SyL_OAuthSignatureMethodHMACSHA1.php';
            $signature_method = new SyL_OAuthSignatureMethodHMACSHA1();
            break;
        default:
            throw new SyL_InvalidParameterException('invalid OAuth signature method (' . $request->getSignatureMethod() . ')');
        }

        $oauth_client = new SyL_OAuthClient($host, $port, $timeout, $ssl);
        $oauth_request = $oauth_client->createRequest($path, $method);
        $oauth_request->setConsumer($this->oauth_parameters['consumer_key'], $this->oauth_parameters['consumer_secret']);
        $oauth_request->setCallback($callback);
        $oauth_request->setSignatureMethod($signature_method);
        $oauth_response = $oauth_client->getRequestToken($oauth_request);
        
        
    }

    /**
     * OAuth Access Token を取得する
     * 
     * @param string OAuth Verifier
     */
    public function getAccessToken($verifier)
    {
        $request = $this->createOAuthRequest();

        $ssl     = $request->isSsl();
        $host    = $request->getRequestHost();
        $port    = $request->getRequestPort();
        $timeout = $request->getTimeout();
        $method  = $request->getRequestMethod();
        $path    = $request->getAccessTokenPath();
        $response_class = $request->getResponseClass();

        include_once dirname(__FILE__) . '/../OAuth/SyL_OAuthClient.php';

        $signature_method = null;
        switch ($request->getSignatureMethod()) {
        case 'TEXT':
            include_once dirname(__FILE__) . '/../OAuth/SyL_OAuthSignatureMethodTEXT.php';
            $signature_method = new SyL_OAuthSignatureMethodTEXT();
            break;
        case 'HMACSHA1':
            include_once dirname(__FILE__) . '/../OAuth/SyL_OAuthSignatureMethodHMACSHA1.php';
            $signature_method = new SyL_OAuthSignatureMethodHMACSHA1();
            break;
        default:
            throw new SyL_InvalidParameterException('invalid OAuth signature method (' . $request->getSignatureMethod() . ')');
        }

        $oauth_client = new SyL_OAuthClient($host, $port, $timeout, $ssl);
        $oauth_request = $oauth_client->createRequest($path, $method);
        $oauth_request->setConsumer($this->oauth_parameters['consumer_key'], $this->oauth_parameters['consumer_secret']);
        $oauth_request->setToken($this->oauth_parameters['oauth_token'], $this->oauth_parameters['oauth_token_secret']);
        $oauth_request->setVerifier($verifier);
        $oauth_request->setSignatureMethod($signature_method);
        $oauth_response = $oauth_client->getAccessToken($oauth_request);
    }

    /**
     * OAuth リクエストクラスを読み込む
     *
     * @return SyL_WebServiceRequestAbstract WEBサービスリクエストオブジェクト
     * @throws SyL_FileNotFoundException WEBサービスリクエスト／レスポンスクラスファイルが見つからない場合
     * @throws SyL_NotImplementedException WEBサービスリクエスト／レスポンスクラスに指定の基底クラスが無い場合
     */
    private function createOAuthRequest()
    {
        static $cache = null;
        if (!$cache) {
            include_once 'SyL_WebServiceOAuthRequestAbstract.php';
            $request_class = 'SyL_WebService' . $this->domain . 'OAuthRequest';
            $base_file = $this->domain_dir . '/' . $request_class . '.php';
            if (!is_file($base_file)) {
                throw new SyL_WebServiceNotFoundException("WebService not implemented in SyL Framework (OAuth api request base file not found: `{$base_file}')");
            }
            include_once $base_file;
            if (!is_subclass_of($request_class, 'SyL_WebServiceOAuthRequestAbstract')) {
                throw new SyL_NotImplementedException("WebService API OAuth request class not implemented 'SyL_WebServiceOAuthRequestAbstract' class");
            }

            include_once 'SyL_WebServiceOAuthResponseAbstract.php';
            $response_class = 'SyL_WebService' . $this->domain . 'OAuthRequest';
            $base_file = $this->domain_dir . '/' . $response_class . '.php';
            if (!is_file($base_file)) {
                throw new SyL_WebServiceNotFoundException("WebService not implemented in SyL Framework (OAuth api request base file not found: `{$base_file}')");
            }
            include_once $base_file;
            if (!is_subclass_of($response_class, 'SyL_WebServiceOAuthResponseAbstract')) {
                throw new SyL_NotImplementedException("WebService API OAuth request class not implemented 'SyL_WebServiceOAuthResponseAbstract' class");
            }
            $cache = new $request_class($response_class);
        }

        return clone $cache;
    }

    /**
     * WEBサービスリクエストオブジェクトを作成する
     * 
     * 引数例） Search.Web
     * 
     * @return SyL_WebServiceRequestAbstract WEBサービスリクエストオブジェクト
     * @throws SyL_FileNotFoundException WEBサービスリクエスト／レスポンスクラスファイルが見つからない場合
     * @throws SyL_NotImplementedException WEBサービスリクエスト／レスポンスクラスに指定の基底クラスが無い場合
     */
    public function createRequest($api)
    {
        static $cache = array();

        $api_request_class  = '';
        $api_response_class = '';
        if (isset($cache[$api])) {
            $api_request_class  = $cache[$api]['request_class'];
            $api_response_class = $cache[$api]['response_class'];
        } else {
            $parts = array_map('ucfirst', explode('.', $api));
            $api_dir = $this->domain_dir . '/' . implode('/', $parts);
            $api_request_class  = 'SyL_WebService' . implode('', $parts) . 'Request';
            $api_response_class = 'SyL_WebService' . implode('', $parts) . 'Response';
            $api_request_file  = $api_dir .  '/' . $api_request_class  . '.php';
            $api_response_file = $api_dir .  '/' . $api_response_class . '.php';

            if (!is_file($api_request_file)) {
                throw new SyL_FileNotFoundException("WebService API request file not found ({$api_request_file})");
            }
            include_once $api_request_file;
            if (!is_subclass_of($api_request_class, 'SyL_WebServiceRequestAbstract')) {
                throw new SyL_NotImplementedException("WebService API request class not implemented 'SyL_WebServiceRequestAbstract' class");
            }

            if (!is_file($api_response_file)) {
                throw new SyL_FileNotFoundException("WebService API response file not found ({$api_response_file})");
            }
            include_once $api_response_file;
            if (!is_subclass_of($api_response_class, 'SyL_WebServiceResponseAbstract')) {
                throw new SyL_NotImplementedException("WebService API response class not implemented 'SyL_WebServiceResponseAbstract' class");
            }

            $cache[$api] = array();
            $cache[$api]['request_class']  = $api_request_class;
            $cache[$api]['response_class'] = $api_response_class;
        }
        return new $api_request_class($api_response_class);
    }

    /**
     * WEB APIを実行する
     * 
     * @param SyL_WebServiceRequestAbstract WEBサービスリクエストオブジェクト
     * @return SyL_WebServiceResponseAbstract WEBサービスレスポンスオブジェクト
     */
    public function sendRequest(SyL_WebServiceRequestAbstract $request)
    {
        $request->validate();

        $type    = $request->getRequestType();
        $ssl     = $request->isSsl();
        $host    = $request->getRequestHost();
        $port    = $request->getRequestPort();
        $timeout = $request->getTimeout();
        $method  = $request->getRequestMethod();
        $path    = $request->getRequestPath();
        $response_class = $request->getResponseClass();

        $response = null;

        switch ($type) {
        case 'rest':
            $client = new SyL_HttpClient($host, $port, $timeout, $ssl);
            $http_request = $client->createRequest($path, $method);
            foreach ($request->gets() as $name => $value) {
                $http_request->setParameter($name, $value);
            }
            $http_response = $client->sendRequest($http_request);
            $response = new $response_class($http_response->getStatus(), $http_response->getBody(), $http_response->getHeaders());
            $response->validate();
            break;
//        case 'xmlrpc':
//            break;
//        case 'soap':
//            break;
        default:
            throw new SyL_InvalidParameterException("invalid request type ({$type})");
        }

        return $response;
    }
}
