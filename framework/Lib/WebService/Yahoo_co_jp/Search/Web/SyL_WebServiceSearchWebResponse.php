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

/** Yahoo! Japan 検索レスポンスクラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResponse.php';
/** Yahoo! Japan ウェブ検索レスポンス結果レコード情報クラス */
require_once 'SyL_WebServiceSearchWebResult.php';

/**
 * Yahoo! Japan ウェブ検索レスポンスクラス
 *
 * 詳細は、Yahoo!デベロッパーネットワーク参照
 *   http://developer.yahoo.co.jp/
 * ウェブ検索
 *   http://developer.yahoo.co.jp/webapi/search/websearch/v1/websearch.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchWebResponse extends SyL_WebServiceSearchResponse
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
            $this->results[$this->index] = new SyL_WebServiceSearchWebResult();
            $this->results[$this->index]->setCache(new SyL_WebServiceSearchResultCache());
            break;
        // ページのタイトル
        case '/ResultSet/Result/Title': $this->results[$this->index]->setTitle($text); break;
        // ページに関連するテキストサマリー
        case '/ResultSet/Result/Summary': $this->results[$this->index]->setSummary($text); break;
        // ページのURL
        case '/ResultSet/Result/Url': $this->results[$this->index]->setUrl($text); break;
        // ページのリンクURL
        case '/ResultSet/Result/ClickUrl': $this->results[$this->index]->setClickUrl($text); break;
        // ページが最後に修正された日付
        case '/ResultSet/Result/ModificationDate': $this->results[$this->index]->setModificationDate($text); break;
        // ページのMIMEタイプ
        case '/ResultSet/Result/MimeType': $this->results[$this->index]->setMimeType($text); break;
        // キャシュ結果のURL
        case '/ResultSet/Result/Cache/Url': $this->results[$this->index]->getCache()->setUrl($text); break;
        // キャシュ結果のサイズ
        case '/ResultSet/Result/Cache/Size': $this->results[$this->index]->getCache()->setSize($text); break;

        // その他
        default: parent::doElement($current_path, $attributes, $text); break;
        }
    }
}
