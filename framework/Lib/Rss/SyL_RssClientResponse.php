<?php
/**
 * -----------------------------------------------------------------------------
 *
 * SyL - PHP Application Framework
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
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** HTTPレスポンスクラス */
require_once dirname(__FILE__) . '/../Http/SyL_HttpClientResponse.php';
/** RSS要素オブジェクト変換クラス */
require_once 'SyL_RssConverter.php';

/**
 * RSSリクエストレスポンスクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_RssClientResponse extends SyL_HttpClientResponse
{
    /**
     * RSS要素オブジェクト
     *
     * @var SyL_RssElementRss
     */
    private $rss = null;

    /**
     * コンストラクタ
     * 
     * @param string レスポンス本文
     * @param string RSSバージョン
     * @param bool ローカルリソースフラグ
     */
    public function __construct($data, $rss_version='2.0', $local_resource=false)
    {
        if (!$local_resource) {
            parent::__construct($data); // ここではエンコーディング変換無し
            $data = $this->getBody();
        }
        $this->rss = SyL_RssConverter::toObject($data, $rss_version, SyL_HttpClient::getClientEncoding());
    }

    /**
     * RSS要素オブジェクトを取得する
     *
     * @return SyL_RssElementRss RSS要素オブジェクト
     */
    public function getRss()
    {
        return $this->rss;
    }

    /**
     * チャンネルオブジェクトを取得する
     *
     * @return SyL_RssElementChannel チャンネルオブジェクト
     */
    public function getChannel()
    {
        return $this->rss->getChannel();
    }

    /**
     * アイテム要素を取得する
     *
     * @return array アイテム要素
     */
    public function getItems()
    {
        return $this->rss->getChannel()->getItems();
    }
}
