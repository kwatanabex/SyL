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

/**
 * Livedoor お天気Webサービスレスポンス結果画像情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherImage extends SyL_WebServiceResultAbstract
{
    /**
     * 天気
     *
     * @var string
     */
     private $title = null;
    /**
     * リクエストされたデータの地域に該当するlivedoor 天気情報のURL
     *
     * @var string
     */
     private $link = null;
    /**
     * 天気アイコンのURL
     *
     * @var string
     */
     private $url = null;
    /**
     * 天気アイコンの幅
     *
     * @var int
     */
     private $width = null;
    /**
     * 天気アイコンの高さ
     *
     * @var int
     */
     private $height = null;

    /**
     * 天気を取得する
     *
     * @return string 天気
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * 天気をセットする
     *
     * @param string 天気
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * リクエストされたデータの地域に該当するlivedoor 天気情報のURLを取得する
     *
     * @return string リクエストされたデータの地域に該当するlivedoor 天気情報のURL
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * リクエストされたデータの地域に該当するlivedoor 天気情報のURLをセットする
     *
     * @param string リクエストされたデータの地域に該当するlivedoor 天気情報のURL
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * 天気アイコンのURLを取得する
     *
     * @return string 天気アイコンのURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 天気アイコンのURLをセットする
     *
     * @param string 天気アイコンのURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 天気アイコンの幅を取得する
     *
     * @return int 天気アイコンの幅
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * 天気アイコンの幅をセットする
     *
     * @param int 天気アイコンの幅
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * 天気アイコンの高さを取得する
     *
     * @return int 天気アイコンの高さ
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * 天気アイコンの高さをセットする
     *
     * @param int 天気アイコンの高さ
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }
}
