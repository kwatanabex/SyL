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
 * Yahoo! Japan 検索レスポンスクラス
 *
 * 詳細は、Yahoo!デベロッパーネットワーク参照
 *   http://developer.yahoo.co.jp/
 * 検索
 *   http://developer.yahoo.co.jp/webapi/search/
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchResponse extends SyL_WebServiceYahoo_co_jpResponse
{
    /**
     * データ内のマッチしたクエリー数
     *
     * @var int
     */
     private $total_results_available = 0;
    /**
     * 返却され、かつマッチしたクエリーの数
     *
     * @var int 
     */
    private $total_results_returned = 0;
    /**
     * 全検索結果の最初のポジション
     *
     * @var int 
     */
    private $first_result_position = 0;

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
        case '/ResultSet':
            if (isset($attributes['totalResultsAvailable'])) {
                $this->total_results_available = (int)$attributes['totalResultsAvailable'];
            }
            if (isset($attributes['totalResultsReturned'])) {
                $this->total_results_returned = (int)$attributes['totalResultsReturned'];
            }
            if (isset($attributes['firstResultPosition'])) {
                $this->first_result_position = (int)$attributes['firstResultPosition'];
            }
            break;

        // その他
        default: parent::doElement($current_path, $attributes, $text); break;
        }
    }

    /**
     * データ内のマッチしたクエリー数を取得する
     *
     * @return int データ内のマッチしたクエリー数
     */
    public function getTotalResultsAvailable()
    {
        return $this->total_results_available;
    }

    /**
     * 返却され、かつマッチしたクエリーの数を取得する
     *
     * @return int 返却され、かつマッチしたクエリーの数
     */
    public function getTotalResultsReturned()
    {
        return $this->total_results_returned;
    }

    /**
     * 返却され、かつマッチしたクエリーの数を取得する
     *
     * @return int 返却され、かつマッチしたクエリーの数
     */
    public function getFirstResultPosition()
    {
        return $this->first_result_position;
    }
}
