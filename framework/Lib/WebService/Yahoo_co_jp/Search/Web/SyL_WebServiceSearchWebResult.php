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

/** Yahoo! Japan ウェブ検索レスポンス結果キャッシュ情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResultCache.php';

/**
 * Yahoo! Japan ウェブ検索レスポンス結果レコード情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchWebResult extends SyL_WebServiceResultAbstract
{
    /**
     * ページのタイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * ページに関連するテキストサマリー
     *
     * @var string
     */
     private $summary = null;
    /**
     * ページのURL
     *
     * @var string
     */
     private $url = null;
    /**
     * ページのリンクURL
     *
     * @var string
     */
     private $clickUrl = null;
    /**
     * ページのMIMEタイプ
     *
     * @var string
     */
     private $mimeType = null;
    /**
     * ページが最後に修正された日付
     *
     * @var string
     */
     private $modificationDate = null;
    /**
     * キャシュ結果
     *
     * @var SyL_WebServiceSearchResultCache
     */
     private $cache = null;

    /**
     * ページのタイトルを取得する
     *
     * @return string ページのタイトル
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * ページのタイトルをセットする
     *
     * @param string ページのタイトル
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * ページに関連するテキストサマリーを取得する
     *
     * @return string ページに関連するテキストサマリー
     */
    public function getSummary()
    {
        return $this->summary;
    }
    /**
     * ページに関連するテキストサマリーをセットする
     *
     * @param string ページに関連するテキストサマリー
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * ページのURLを取得する
     *
     * @return string ページのURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * ページのURLをセットする
     *
     * @param string ページのURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * ページのリンクURLを取得する
     *
     * @return string ページのリンクURL
     */
    public function getClickUrl()
    {
        return $this->clickUrl;
    }
    /**
     * ページのリンクURLをセットする
     *
     * @param string ページのリンクURL
     */
    public function setClickUrl($clickUrl)
    {
        $this->clickUrl = $clickUrl;
    }

    /**
     * ページのMIMEタイプを取得する
     *
     * @return string ページのMIMEタイプ
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }
    /**
     * ページのMIMEタイプをセットする
     *
     * @param string ページのMIMEタイプ
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * ページが最後に修正された日付を取得する
     *
     * @return string ページが最後に修正された日付
     */
    public function getModificationDate()
    {
        return $this->modificationDate;
    }
    /**
     * ページが最後に修正された日付タイプをセットする
     *
     * @param string ページが最後に修正された日付
     */
    public function setModificationDate($modificationDate)
    {
        $this->modificationDate = $modificationDate;
    }

    /**
     * キャシュ結果を取得する
     *
     * @return SyL_WebServiceSearchResultCache キャシュ結果
     */
    public function getCache()
    {
        return $this->cache;
    }
    /**
     * キャシュ結果
     *
     * @param SyL_WebServiceSearchResultCache キャシュ結果
     */
    public function setCache(SyL_WebServiceSearchResultCache $cache)
    {
        $this->cache = $cache;
    }
}
