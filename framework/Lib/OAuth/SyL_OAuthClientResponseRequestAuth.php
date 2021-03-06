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

/** OAuth レスポンスクラス */
require_once 'SyL_OAuthClientResponseAbstract.php';

/**
 * OAuth RequestToken取得レスポンスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Http
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_OAuthClientResponseRequestAuth extends SyL_OAuthClientResponseAbstract
{
    /**
     * 取得結果パラメータ
     *
     * @var array
     */
    protected $parameters = array(
        'location' => null,
    );

    /**
     * コンストラクタ
     * 
     * @param SyL_HttpClientResponse HTTPレスポンスオブジェクト
     */
    public function __construct(SyL_HttpClientResponse $response)
    {
        parent::__construct($response);
        $this->parameters['location'] = $response->getLocation();
    }

    /**
     * レスポンスBodyをパースし、プロパティ化する
     * 
     * @param string レスポンスBody
     */
    protected function parseBody($body)
    {
    }
}
