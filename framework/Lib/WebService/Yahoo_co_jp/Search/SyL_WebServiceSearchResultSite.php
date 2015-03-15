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

/**
 * Yahoo! Japan ブログ検索レスポンス結果サイト情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchResultSite extends SyL_WebServiceResultAbstract
{
    /**
     * 記事を掲載しているブログのタイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * 記事を掲載しているブログのURL
     *
     * @var string
     */
     private $url = null;

    /**
     * 記事を掲載しているブログのタイトルを取得する
     *
     * @return string 記事を掲載しているブログのタイトル
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * 記事を掲載しているブログのタイトルをセットする
     *
     * @param string 記事を掲載しているブログのタイトル
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 記事を掲載しているブログのURLを取得する
     *
     * @return string 記事を掲載しているブログのURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 記事を掲載しているブログのURLをセットする
     *
     * @param string 記事を掲載しているブログのURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

}
