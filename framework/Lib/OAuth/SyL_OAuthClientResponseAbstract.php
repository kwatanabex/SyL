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
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/**
 * OAuthレスポンスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_OAuthClientResponseAbstract
{
    /**
     * HTTPレスポンスオブジェクト
     *
     * @var SyL_HttpClientResponse
     */
    private $response = null;
    /**
     * 取得結果パラメータ
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * コンストラクタ
     * 
     * @param SyL_HttpClientResponse HTTPレスポンスオブジェクト
     */
    public function __construct(SyL_HttpClientResponse $response)
    {
        $this->parseBody($response->getBody());
        $this->response = $response;
    }

    /**
     * レスポンスBodyをパースし、プロパティ化する
     * 
     * @param string レスポンスBody
     */
    protected function parseBody($body)
    {
        $result = array();
        parse_str($body, $result);
        if (get_magic_quotes_gpc()) {
            $result = array_map('stripslashes', $result);
        }
        $result = array_change_key_case($result, CASE_LOWER);
        foreach ($result as $name => $value) {
            if (array_key_exists($name, $this->parameters)) {
                $this->parameters[$name] = $value;
            }
        }
    }

    /**
     * HTTPレスポンスオブジェクトを取得する
     * 
     * @param string リクエスト結果
     */
    public function getHttpResponse()
    {
        return $this->response;
    }

    /**
     * プロパティを取得する
     * 
     * @param string プロパティ名
     * @return string プロパティ値
     */
    public function __get($name) 
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->parameters)) {
            return $this->parameters[$name];
        } else {
            throw new SyL_InvalidParameterException("invalid property ({$name})");
        }
    }
}
