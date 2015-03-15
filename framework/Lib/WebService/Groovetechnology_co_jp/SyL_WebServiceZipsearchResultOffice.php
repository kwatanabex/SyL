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
 * Groove Technolorgy 郵便番号検索レスポンス結果、事業所の個別郵便番号情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceZipsearchResultOffice extends SyL_WebServiceResultAbstract
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
     * 大口事業所等名の小字名、丁目、番地等
     *
     * @var string
     */
     private $street = null;
    /**
     * 大口事業所等名
     *
     * @var string
     */
     private $office_name = null;
    /**
     * 大口事業所等名のよみ（カタカナ）
     *
     * @var string
     */
     private $office_name_yomi = null;

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
     * 大口事業所等名の小字名、丁目、番地等を取得する
     *
     * @return string 大口事業所等名の小字名、丁目、番地等
     */
    public function getStreet()
    {
        return $this->street;
    }
    /**
     * 大口事業所等名の小字名、丁目、番地等をセットする
     *
     * @param string 大口事業所等名の小字名、丁目、番地等
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * 大口事業所等名を取得する
     *
     * @return string 大口事業所等名
     */
    public function getOffice_name()
    {
        return $this->office_name;
    }
    /**
     * 大口事業所等名をセットする
     *
     * @param string 大口事業所等名
     */
    public function setOffice_name($office_name)
    {
        $this->office_name = $office_name;
    }

    /**
     * 大口事業所等名のよみ（カタカナ）を取得する
     *
     * @return string 大口事業所等名のよみ（カタカナ）
     */
    public function getOffice_name_yomi()
    {
        return $this->office_name_yomi;
    }
    /**
     * 大口事業所等名のよみ（カタカナ）をセットする
     *
     * @param string 大口事業所等名のよみ（カタカナ）
     */
    public function setOffice_name_yomi($office_name_yomi)
    {
        $this->office_name_yomi = $office_name_yomi;
    }
}
