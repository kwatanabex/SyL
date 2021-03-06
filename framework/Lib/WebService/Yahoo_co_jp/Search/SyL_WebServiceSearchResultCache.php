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
 * Yahoo! Japan ウェブ検索レスポンス結果キャッシュ情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchResultCache extends SyL_WebServiceResultAbstract
{
    /**
     * キャシュ結果のURL
     *
     * @var string
     */
     private $url = null;
    /**
     * キャシュ結果のサイズ
     *
     * @var int
     */
     private $size = null;

    /**
     * キャシュ結果のURLを取得する
     *
     * @return string キャシュ結果のURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * キャシュ結果のURLをセットする
     *
     * @param string キャシュ結果のURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * キャシュ結果のサイズを取得する
     *
     * @return int キャシュ結果のサイズ
     */
    public function getSize()
    {
        return $this->size;
    }
    /**
     * キャシュ結果のサイズをセットする
     *
     * @param int キャシュ結果のサイズ
     */
    public function setSize($size)
    {
        $this->size = $size;
    }
}
