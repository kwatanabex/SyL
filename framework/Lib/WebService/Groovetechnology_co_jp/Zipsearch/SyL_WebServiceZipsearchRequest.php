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
 * Groove Technolorgy 郵便番号検索リクエストクラス
 *
 * 詳細は、郵便番号検索API参照
 *   http://groovetechnology.co.jp/webservice/zipsearch/index.html
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceZipsearchRequest extends SyL_WebServiceGroovetechnology_co_jpRequest
{
    /**
     * WEBサービスサーバーのホスト名
     *
     * @var string
     */
    protected $host = 'groovetechnology.co.jp';
    /**
     * 実行URLパス
     *
     * @var string
     */
    protected $path = '/ZipSearchService/v1/zipsearch';

    /**
     * 郵便番号をセットする
     *
     * @param string 郵便番号
     */
    public function setZipcode($value)
    {
        if (preg_match('/^[0-9]{3}\-?[0-9]{0,4}$/', $value)) {
            parent::set('zipcode', $value);
        } else {
            throw new SyL_InvalidParameterException("Invalid parameter `zipcode' ({$value})");
        }
    }

    /**
     * 住所の一部をセットする
     *
     * @param string 住所の一部
     */
    public function setWord($value)
    {
        parent::set('word', $value);
    }

    /**
     * 出力するフォーマットをセットする
     *
     * xmlのみサポート
     *
     * @param string 出力するフォーマット
     */
    public function setFormat($value)
    {
        switch ($value) {
        case 'xml':
            parent::set('format', $value);
            break;
        case 'json':
        case 'PHP':
            throw new SyL_NotImplementedException("format parameter `xml' only");
        default:
            throw new SyL_InvalidParameterException("Invalid parameter `format' ({$value})");
        }
    }

    /**
     * jsonpで返却されるcallback関数名をセットする
     *
     * XML結果のみ対応しているので、他は選択不可
     *
     * @param string jsonpで返却されるcallback関数名
     */
    public function setCallback($value)
    {
        throw new SyL_NotImplementedException("format parameter `xml' only");
    }

    /**
     * 入力する文字の文字コードをセットする
     *
     * UTF-8のみサポート
     *
     * @param string 入力する文字の文字コード
     */
    public function setIe($value)
    {
        switch ($value) {
        case 'UTF-8':
            parent::set('ie', $value);
            break;
        case 'Shift_JIS':
        case 'EUC-JP':
        case 'ISO-2022-JP':
            throw new SyL_NotImplementedException("ie parameter `UTF-8' only");
        default:
            throw new SyL_InvalidParameterException("Invalid parameter `ie' ({$value})");
        }
    }

    /**
     * 出力時の文字コードをセットする
     *
     * UTF-8のみサポート
     *
     * @param string 出力時の文字コード
     */
    public function setOe($value)
    {
        switch ($value) {
        case 'UTF-8':
            parent::set('oe', $value);
            break;
        case 'Shift_JIS':
        case 'EUC-JP':
        case 'ISO-2022-JP':
            throw new SyL_NotImplementedException("oe parameter `UTF-8' only");
        default:
            throw new SyL_InvalidParameterException("Invalid parameter `oe' ({$value})");
        }
    }

    /**
     * リクエスト内容のチェック
     *
     * @throws SyL_InvalidParameterException リクエストパラメータにエラーがある場合
     */
    public function validate()
    {
        parent::validate();
        if (($this->get('zipcode') === null) && ($this->get('word') === null)) {
            throw new SyL_InvalidParameterException("parameter not setting `zipcode' and `word'");
        }
    }
}
