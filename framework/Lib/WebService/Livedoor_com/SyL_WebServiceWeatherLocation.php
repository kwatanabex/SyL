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
 * Livedoor お天気Webサービスレスポンス結果地域情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceWeatherLocation extends SyL_WebServiceResultAbstract
{
    /**
     * 市区町村名
     *
     * @var string
     */
     private $title = null;
    /**
     * 対応するlivedoor 天気情報のURL
     *
     * @var string
     */
     private $link = null;
    /**
     * ピンポイント天気予報の発表時間
     *
     * @var string
     */
     private $publictime = null;

    /**
     * 市区町村名を取得する
     *
     * @return string 市区町村名
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * 市区町村名をセットする
     *
     * @param string 市区町村名
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 対応するlivedoor 天気情報のURLを取得する
     *
     * @return string 対応するlivedoor 天気情報のURL
     */
    public function getLink()
    {
        return $this->link;
    }
    /**
     * 対応するlivedoor 天気情報のURLをセットする
     *
     * @param string 対応するlivedoor 天気情報のURL
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * ピンポイント天気予報の発表時間を取得する
     *
     * @return string ピンポイント天気予報の発表時間
     */
    public function getPublictime()
    {
        return $this->publictime;
    }
    /**
     * ピンポイント天気予報の発表時間をセットする
     *
     * @param string ピンポイント天気予報の発表時間
     */
    public function setPublictime($publictime)
    {
        $this->publictime = $publictime;
    }
}
