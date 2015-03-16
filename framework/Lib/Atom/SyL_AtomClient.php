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
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 * -----------------------------------------------------------------------------
 */

/** HTTPクライアントクラス */
require_once dirname(__FILE__) . '/../Http/SyL_HttpClient.php';
/** ファイル操作クラス */
require_once dirname(__FILE__) . '/../File/SyL_FileAbstract.php';
/** Atomクライアントレスポンスクラス */
require_once 'SyL_AtomClientResponse.php';

/**
 * ATOMクライアントクラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.Rss
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_AtomClient extends SyL_HttpClient
{
    /**
     * RSSバージョン
     *
     * nullの場合は自動判定
     *
     * @var string
     */
    private $rss_version = '2.0';

    /**
     * リソースからレスポンスオブジェクトを取得する
     * 
     * @param string リソース名
     * @return SyL_RssClientResponse レスポンスオブジェクト
     */
    public function getResponseFromResource($resource_name)
    {
        return $this->createResponse(SyL_FileAbstract::readContents($resource_name), true);
    }

    /**
     * レスポンスオブジェクトを作成する
     * 
     * @param string HTTPレスポンス
     * @param bool ローカルリソースフラグ
     * @return SyL_RssClientResponse レスポンスオブジェクト
     */
    protected function createResponse($data, $local_resource=false)
    {
        return new SyL_AtomClientResponse($data, $local_resource);
    }
}
