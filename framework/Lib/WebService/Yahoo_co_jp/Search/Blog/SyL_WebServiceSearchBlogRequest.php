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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** Yahoo! Japan 検索リクエストクラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchRequest.php';

/**
 * Yahoo! Japan ブログ検索リクエストクラス
 *
 * 詳細は、Yahoo!デベロッパーネットワーク参照
 *   http://developer.yahoo.co.jp/
 * ブログ検索
 *   http://developer.yahoo.co.jp/webapi/search/blogsearch/v1/blogsearch.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchBlogRequest extends SyL_WebServiceSearchRequest
{
    /**
     * 実行URLパス
     *
     * @var string
     */
    protected $path = '/BlogSearchService/V1/blogSearch';

    /**
     * 指定検索の種類をセットする
     * (all, any, phrase)
     *
     * @param string 指定検索の種類
     */
    public function setType($value)
    {
        switch ($value) {
        case 'article':
        case 'channel':
            parent::set('type', $value);
            break;
        default:
            throw new SyL_InvalidParameterException("invalid parameter `type'. only article/channel ({$value})");
        }
    }

    /**
     * 日付絞り込みをセットする
     *
     * @param string 日付絞り込み
     */
    public function setTerm($value)
    {
        switch ($value) {
        case 'day':
        case 'week':
        case 'month':
            parent::set('term', $value);
            break;
        default:
            throw new SyL_InvalidParameterException("invalid parameter `term'. only day/week/month ({$value})");
        }
    }

    /**
     * レスポンス選択をセットする
     *
     * xmlのみサポート
     *
     * @param string レスポンス選択
     */
    public function setOutput($value)
    {
        switch ($value) {
        case 'xml':
            parent::set('output', $value);
            break;
        case 'php':
        case 'json':
            throw new SyL_NotImplementedException("output parameter `xml' only");
        default:
            throw new SyL_InvalidParameterException("invalid parameter `output'. only xml/php/json ({$value})");
        }
    }

    /**
     * jsonpで返却されるcallback関数名をセットする
     *
     * XML結果のみ対応しているので、他は選択不可
     *
     * @param string jsonpで返却されるcallback関数名
     */
    public function setCallback($value)
    {
        throw new SyL_NotImplementedException("output parameter `xml' only");
    }
}
