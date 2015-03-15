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
 * Groove Technolorgy 郵便番号検索レスポンス結果、住所の郵便番号情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceZipsearchResultAddress extends SyL_WebServiceResultAbstract
{
    /**
     * 郵便番号
     *
     * @var string
     */
    private $zipcode = null;
    /**
     * 都道府県名
     *
     * @var string
     */
    private $prefecture = null;
    /**
     * 市区町村名
     *
     * @var string
     */
     private $city = null;
    /**
     * 町域名
     *
     * @var string
     */
     private $town = null;
    /**
     * 都道府県名のよみ（カタカナ）
     *
     * @var string
     */
     private $prefecture_yomi = null;
    /**
     * 市区町村名のよみ（カタカナ）
     *
     * @var string
     */
     private $city_yomi = null;
    /**
     * 町域名のよみ（カタカナ）
     *
     * @var string
     */
     private $town_yomi = null;

    /**
     * 郵便番号を取得する
     *
     * @return string 郵便番号
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }
    /**
     * 郵便番号をセットする
     *
     * @param string 郵便番号
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * 都道府県名を取得する
     *
     * @return string 都道府県名
     */
    public function getPrefecture()
    {
        return $this->prefecture;
    }
    /**
     * 都道府県名をセットする
     *
     * @param string 都道府県名
     */
    public function setPrefecture($prefecture)
    {
        $this->prefecture = $prefecture;
    }

    /**
     * 市区町村名を取得する
     *
     * @return string 市区町村名
     */
    public function getCity()
    {
        return $this->city;
    }
    /**
     * 市区町村名をセットする
     *
     * @param string 市区町村名
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * 町域名を取得する
     *
     * @return string 町域名
     */
    public function getTown()
    {
        return $this->town;
    }
    /**
     * 町域名をセットする
     *
     * @param string 町域名
     */
    public function setTown($town)
    {
        $this->town = $town;
    }

    /**
     * 都道府県名のよみ（カタカナ）を取得する
     *
     * @return string 都道府県名のよみ（カタカナ）
     */
    public function getPrefecture_yomi()
    {
        return $this->prefecture_yomi;
    }
    /**
     * 都道府県名のよみ（カタカナ）をセットする
     *
     * @param string 都道府県名のよみ（カタカナ）
     */
    public function setPrefecture_yomi($prefecture_yomi)
    {
        $this->prefecture_yomi = $prefecture_yomi;
    }

    /**
     * 市区町村名のよみ（カタカナ）を取得する
     *
     * @return string 市区町村名のよみ（カタカナ）
     */
    public function getCity_yomi()
    {
        return $this->city_yomi;
    }
    /**
     * 市区町村名のよみ（カタカナ）をセットする
     *
     * @param string 市区町村名のよみ（カタカナ）
     */
    public function setCity_yomi($city_yomi)
    {
        $this->city_yomi = $city_yomi;
    }

    /**
     * 町域名のよみ（カタカナ）を取得する
     *
     * @return string 町域名のよみ（カタカナ）
     */
    public function getTown_yomi()
    {
        return $this->town_yomi;
    }
    /**
     * 町域名のよみ（カタカナ）をセットする
     *
     * @param string 町域名のよみ（カタカナ）
     */
    public function setTown_yomi($town_yomi)
    {
        $this->town_yomi = $town_yomi;
    }
}
