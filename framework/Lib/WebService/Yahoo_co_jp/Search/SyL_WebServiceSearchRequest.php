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
 * Yahoo! Japan 検索リクエストクラス
 *
 * 詳細は、Yahoo!デベロッパーネットワーク参照
 *   http://developer.yahoo.co.jp/
 * 検索
 *   http://developer.yahoo.co.jp/webapi/search/
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchRequest extends SyL_WebServiceYahoo_co_jpRequest
{
    /**
     * WEBサービスサーバーのホスト名
     *
     * @var string
     */
    protected $host = 'search.yahooapis.jp';

    /**
     * 検索クエリーをセットする
     *
     * @param string 検索クエリー
     */
    public function setQuery($value)
    {
        parent::set('query', $value);
    }

    /**
     * 返却結果の数をセットする
     *
     * @param int 返却結果の数
     */
    public function setResults($value)
    {
        if (preg_match('/^\d+$/', $value) && ($value >= 0) && ($value <= 50)) {
            parent::set('results', $value);
        } else {
            throw new SyL_InvalidParameterException("invalid property `results'. only numeric [0-50] ({$value})");
        }
    }

    /**
     * 返却結果の先頭位置をセットする
     *
     * @param int 返却結果の先頭位置
     */
    public function setStart($value)
    {
        if (preg_match('/^\d+$/', $value) && ($value >= 1)) {
            parent::set('start', $value);
        } else {
            throw new SyL_InvalidParameterException("invalid property `start'. only numeric ({$value})");
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
        if ($this->get('query') === null) {
            throw new SyL_InvalidParameterException("parameter not setting `query'");
        }
    }
}
