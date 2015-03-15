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

/** Groove Technolorgy 郵便番号検索レスポンス結果レコード情報クラス */
require_once 'SyL_WebServiceZipsearchResult.php';

/**
 * Groove Technolorgy 郵便番号検索レスポンスクラス
 *
 * 詳細は、郵便番号検索API参照
 *   http://groovetechnology.co.jp/webservice/zipsearch/index.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceZipsearchResponse extends SyL_WebServiceGroovetechnology_co_jpResponse
{
    /**
     * 事業所レコードのインデックス
     *
     * @var int
     */
     protected $index_office = -1;

    /**
     * コンストラクタ
     * 
     * @param string レスポンスステータスコード
     * @param string リクエスト結果XML
     * @param array レスポンスヘッダ配列
     */
    public function __construct($status_code, $result, array $headers)
    {
        $this->results[0] = new SyL_WebServiceZipsearchResult();
        parent::__construct($status_code, $result, $headers);
    }

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
        // 住所の郵便番号の情報
        case '/groovewebservice/address':
            $this->index++;
            $address = $this->results[0]->getAddress();
            $address[$this->index] = new SyL_WebServiceZipsearchResultAddress();
            $this->results[0]->setAddress($address);
            break;
        // 郵便番号
        case '/groovewebservice/address/zipcode':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setZipcode($text);
            $this->results[0]->setAddress($address);
            break;
        // 都道府県名
        case '/groovewebservice/address/prefecture':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setPrefecture($text);
            $this->results[0]->setAddress($address);
            break;
        // 市区町村名
        case '/groovewebservice/address/city':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setCity($text);
            $this->results[0]->setAddress($address);
            break;
        // 町域名
        case '/groovewebservice/address/town':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setTown($text);
            $this->results[0]->setAddress($address);
            break;
        // 都道府県名のよみ（カタカナ）
        case '/groovewebservice/address/prefecture_yomi':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setPrefecture_yomi($text);
            $this->results[0]->setAddress($address);
            break;
        // 市区町村名のよみ（カタカナ）
        case '/groovewebservice/address/city_yomi':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setCity_yomi($text);
            $this->results[0]->setAddress($address);
            break;
        // 町域名のよみ（カタカナ）
        case '/groovewebservice/address/town_yomi':
            $address = $this->results[0]->getAddress();
            $address[$this->index]->setTown_yomi($text);
            $this->results[0]->setAddress($address);
            break;

        // 事業所の個別郵便番号の情報
        case '/groovewebservice/office':
            $this->index_office++;
            $office = $this->results[0]->getOffice();
            $office[$this->index_office] = new SyL_WebServiceZipsearchResultOffice();
            $this->results[0]->setOffice($office);
            break;
        // 郵便番号
        case '/groovewebservice/office/zipcode':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setZipcode($text);
            $this->results[0]->setOffice($office);
            break;
        // 都道府県名
        case '/groovewebservice/office/prefecture':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setPrefecture($text);
            $this->results[0]->setOffice($office);
            break;
        // 市区町村名
        case '/groovewebservice/office/city':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setCity($text);
            $this->results[0]->setOffice($office);
            break;
        // 町域名
        case '/groovewebservice/office/town':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setTown($text);
            $this->results[0]->setOffice($office);
            break;
        // 大口事業所等名の小字名、丁目、番地等
        case '/groovewebservice/office/street':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setStreet($text);
            $this->results[0]->setOffice($office);
            break;
        // 大口事業所等名
        case '/groovewebservice/office/office_name':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setOffice_name($text);
            $this->results[0]->setOffice($office);
            break;
        // 大口事業所等名のよみ（カタカナ）
        case '/groovewebservice/office/town_yomi':
            $office = $this->results[0]->getOffice();
            $address[$this->index_office]->setTown_yomi($text);
            $this->results[0]->setOffice($office);
            break;

        // その他
        default: parent::doElement($current_path, $attributes, $text); break;
        }
    }
}
