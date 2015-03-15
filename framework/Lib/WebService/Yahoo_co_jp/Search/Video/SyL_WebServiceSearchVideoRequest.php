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
 * Yahoo! Japan 動画検索リクエストクラス
 *
 * 詳細は、Yahoo!デベロッパーネットワーク参照
 *   http://developer.yahoo.co.jp/
 * 動画検索
 *   http://developer.yahoo.co.jp/webapi/search/videosearch/v1/videosearch.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchVideoRequest extends SyL_WebServiceSearchRequest
{
    /**
     * 実行URLパス
     *
     * @var string
     */
    protected $path = '/VideoSearchService/V1/videoSearch';

    /**
     * 指定検索の種類をセットする
     * (all, any, phrase)
     *
     * @param string 指定検索の種類
     */
    public function setType($value)
    {
        switch ($value) {
        case 'all':
        case 'any':
        case 'phrase':
            parent::set('type', $value);
            break;
        default:
            throw new SyL_InvalidParameterException("invalid parameter `type'. only all/any/phrase ({$value})");
        }
    }

    /**
     * 検索する画像の種類をセットする
     *
     * @param string 検索する画像の種類
     */
    public function setFormat($value)
    {
        switch ($value) {
        case 'any':
        case 'avi':
        case 'flash':
        case 'mpeg':
        case 'msmedia':
        case 'quicktime':
        case 'realmedia':
            parent::set('format', $value);
            break;
        default:
            throw new SyL_InvalidParameterException("invalid parameter `format'. only any/avi/flash/mpeg/msmedia/quicktime/realmedia ({$value})");
        }
    }

    /**
     * アダルトコンテンツの検索結果を含めるかどうかをセットする
     *
     * @param string アダルトコンテンツの検索結果を含めるかどうか
     */
    public function setAdult_ok($value)
    {
        if (is_bool($value)) {
            parent::set('adult_ok', ($value ? '1' : ''));
        } else {
            switch ($value) {
            case '1':
            case '':
                parent::set('adult_ok', $value);
                break;
            default:
                throw new SyL_InvalidParameterException("invalid parameter `adult_ok'. only 1 or '' ({$value})");
            }
        }
    }

    /**
     * 検索するドメインをセットする
     *
     * @param string or array 検索するドメイン
     */
    public function setSite($value)
    {
        parent::set('site', $value);
    }
}
