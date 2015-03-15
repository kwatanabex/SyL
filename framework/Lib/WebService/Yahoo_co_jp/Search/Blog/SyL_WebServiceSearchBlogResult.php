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

/** Yahoo! Japan ブログ検索レスポンス結果サイト情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResultSite.php';

/**
 * Yahoo! Japan ブログ検索レスポンス結果レコード情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchBlogResult extends SyL_WebServiceResultAbstract
{
    /**
     * ページのID
     *
     * @var string
     */
     private $id = null;
    /**
     * ページのRSS URL
     *
     * @var string
     */
     private $rssUrl = null;
    /**
     * 記事のタイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * 記事のサマリー
     *
     * @var string
     */
     private $description = null;
    /**
     * 記事のURL
     *
     * @var string
     */
     private $url = null;
    /**
     * 記事の著者
     *
     * @var string
     */
     private $creator = null;
    /**
     * MobileLinkDiscoveryで指定されているURL
     *
     * @var string
     */
     private $mobileLink = null;
    /**
     * 記事を掲載しているブログのタイトル、URL
     *
     * @var SyL_WebServiceSearchResultSite
     */
     private $site = null;
    /**
     * 記事の更新時間
     *
     * @var string
     */
     private $dateTime = null;

    /**
     * ページのIDを取得する
     *
     * @return string ページのID
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * ページのIDをセットする
     *
     * @param string ページのID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * ページのRSS URLを取得する
     *
     * @return string ページのRSS URL
     */
    public function getRssUrl()
    {
        return $this->rssUrl;
    }
    /**
     * ページのRSS URLをセットする
     *
     * @param string ページのRSS URL
     */
    public function setRssUrl($rssUrl)
    {
        $this->rssUrl = $rssUrl;
    }

    /**
     * 記事のタイトルを取得する
     *
     * @return string 記事のタイトル
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * 記事のタイトルをセットする
     *
     * @param string 記事のタイトル
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 記事のサマリーを取得する
     *
     * @return string 記事のサマリー
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * 記事のサマリーをセットする
     *
     * @param string 記事のサマリー
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * 記事のURLを取得する
     *
     * @return string 記事のURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 記事のURLをセットする
     *
     * @param string 記事のURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 記事の著者を取得する
     *
     * @return string 記事の著者
     */
    public function getCreator()
    {
        return $this->creator;
    }
    /**
     * 記事の著者をセットする
     *
     * @param string 記事の著者
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * MobileLinkDiscoveryで指定されているURLを取得する
     *
     * @return string MobileLinkDiscoveryで指定されているURL
     */
    public function getMobileLink()
    {
        return $this->mobileLink;
    }
    /**
     * MobileLinkDiscoveryで指定されているURLをセットする
     *
     * @param string MobileLinkDiscoveryで指定されているURL
     */
    public function setMobileLink($mobileLink)
    {
        $this->mobileLink = $mobileLink;
    }

    /**
     * 記事を掲載しているブログのタイトル、URLを取得する
     *
     * @return SyL_WebServiceSearchResultSite 記事を掲載しているブログのタイトル、URL
     */
    public function getSite()
    {
        return $this->site;
    }
    /**
     * 記事を掲載しているブログのタイトル、URLをセットする
     *
     * @param SyL_WebServiceSearchResultSite 記事を掲載しているブログのタイトル、URL
     */
    public function setSite(SyL_WebServiceSearchResultSite $site)
    {
        $this->site = $site;
    }

    /**
     * 記事の更新時間を取得する
     *
     * @return string 記事の更新時間
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }
    /**
     * 記事の更新時間をセットする
     *
     * @param string 記事の更新時間
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;
    }
}
