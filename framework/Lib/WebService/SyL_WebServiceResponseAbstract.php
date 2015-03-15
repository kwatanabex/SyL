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

/** XMLパーサークラス */
require_once dirname(__FILE__) . '/../Xml/SyL_XmlParserAbstract.php';

/**
 * WEBサービスレスポンスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
abstract class SyL_WebServiceResponseAbstract extends SyL_XmlParserAbstract
{
    /**
     * XMLエンコーディング
     * 
     * @var string
     */
    protected $xml_encoding = 'UTF-8';
    /**
     * レスポンスステータスコード
     * 
     * @var int
     */
    private $status_code = 200;
    /**
     * レスポンスヘッダ
     * 
     * @var array
     */
    private $headers = 200;
    /**
     * レコードのインデックス
     *
     * @var int
     */
     protected $index = -1;
    /**
     * 検索結果データ配列
     *
     * @var array
     */
    protected $results = array();

    /**
     * コンストラクタ
     * 
     * @param string レスポンスステータスコード
     * @param string リクエスト結果XML
     * @param array レスポンスヘッダ配列
     */
    public function __construct($status_code, $result, array $headers)
    {
        $this->status_code = (int)$status_code;
        $this->headers = $headers;

        if ($result) {
            if ($this->xml_encoding) {
                $this->setInputEncoding($this->xml_encoding);
            }
            $this->setData($result);
            $this->parse();
        }
    }

    /**
     * レスポンス内容のチェック
     *
     * @throws SyL_WebServiceResultException 取得結果にエラーがある場合
     */
    public function validate() {}

    /**
     * レスポンスステータスコードを取得する
     *
     * @param int レスポンスステータスコード
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * レスポンスヘッダを取得する
     *
     * @param array レスポンスヘッダ
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 検索結果データを取得する
     *
     * @return array 検索結果データ配列
     */
    public function getResults()
    {
        return $this->results;
    }
}
