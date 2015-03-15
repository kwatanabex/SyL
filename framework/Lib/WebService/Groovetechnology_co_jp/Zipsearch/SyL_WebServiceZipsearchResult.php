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

/** Groove Technolorgy 郵便番号検索レスポンス結果、住所の郵便番号情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceZipsearchResultAddress.php';
/** Groove Technolorgy 郵便番号検索レスポンス結果、事業所の個別郵便番号情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceZipsearchResultOffice.php';

/**
 * Groove Technolorgy 郵便番号検索レスポンス結果レコード情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceZipsearchResult extends SyL_WebServiceResultAbstract
{
    /**
     * 住所の郵便番号の情報
     *
     * @var array
     */
    private $address = array();
    /**
     * 事業所の個別郵便番号の情報
     *
     * @var array
     */
    private $office = array();

    /**
     * 住所の郵便番号の情報を取得する
     *
     * @return array 住所の郵便番号の情報
     */
    public function getAddress()
    {
        return $this->address;
    }
    /**
     * 住所の郵便番号の情報ルをセットする
     *
     * @param array 住所の郵便番号の情報
     */
    public function setAddress(array $address)
    {
        $this->address = $address;
    }

    /**
     * 事業所の個別郵便番号の情報を取得する
     *
     * @return array 事業所の個別郵便番号の情報
     */
    public function getOffice()
    {
        return $this->office;
    }
    /**
     * 事業所の個別郵便番号の情報をセットする
     *
     * @param array 事業所の個別郵便番号の情報
     */
    public function setOffice(array $office)
    {
        $this->office = $office;
    }
}
