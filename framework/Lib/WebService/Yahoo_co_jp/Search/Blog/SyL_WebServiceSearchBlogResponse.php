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

/** Yahoo! Japan 検索レスポンスクラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResponse.php';
/** Yahoo! Japan ブログ検索レスポンス結果レコード情報クラス */
require_once 'SyL_WebServiceSearchBlogResult.php';

/**
 * Yahoo! Japan ブログ検索レスポンスクラス
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
class SyL_WebServiceSearchBlogResponse extends SyL_WebServiceSearchResponse
{
    /**
     * カレント要素のイベント
     *
     * @param string パス
     * @param array 属性配列
     * @param string テキスト
     */
    protected function doElement($current_path, array $attributes, $text)
    {
        switch ($current_path) {
        case '/ResultSet/Result':
            $this->index++;
            $this->results[$this->index] = new SyL_WebServiceSearchBlogResult();
            $this->results[$this->index]->setSite(new SyL_WebServiceSearchResultSite());
            break;
        // ページのID
        case '/ResultSet/Result/Id': $this->results[$this->index]->setId($text); break;
        // ページのRSS URL
        case '/ResultSet/Result/RssUrl': $this->results[$this->index]->setRssUrl($text); break;
        // 記事のタイトル
        case '/ResultSet/Result/Title': $this->results[$this->index]->setTitle($text); break;
        // 記事のサマリー
        case '/ResultSet/Result/Description': $this->results[$this->index]->setDescription($text); break;
        // 記事のURL
        case '/ResultSet/Result/Url': $this->results[$this->index]->setUrl($text); break;
        // 記事の著者
        case '/ResultSet/Result/Creator': $this->results[$this->index]->setCreator($text); break;
        // MobileLinkDiscoveryで指定されているURL
        case '/ResultSet/Result/mobileLink': $this->results[$this->index]->setMobileLink($text); break;
        // 記事を掲載しているブログのタイトル
        case '/ResultSet/Result/Site/Title': $this->results[$this->index]->getSite()->setTitle($text); break;
        // 記事を掲載しているブログのURL
        case '/ResultSet/Result/Site/Url': $this->results[$this->index]->getSite()->setUrl($text); break;
        // 記事の更新時間
        case '/ResultSet/Result/DateTime': $this->results[$this->index]->setDateTime($text); break;

        // その他
        default: parent::doElement($current_path, $attributes, $text); break;
        }
    }
}
