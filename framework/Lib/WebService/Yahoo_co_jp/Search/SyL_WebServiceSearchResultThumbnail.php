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
 * Yahoo! Japan 画像検索レスポンス結果サムネイル情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchResultThumbnail extends SyL_WebServiceResultAbstract
{
    /**
     * サムネイル画像のURL
     *
     * @var string
     */
     private $url = null;
    /**
     * サムネイル画像の高さ
     *
     * @var int
     */
     private $height = null;
    /**
     * サムネイル画像の幅
     *
     * @var int
     */
     private $width = null;

    /**
     * サムネイル画像のURLを取得する
     *
     * @return string サムネイル画像のURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * サムネイル画像のURLをセットする
     *
     * @param string サムネイル画像のURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * サムネイル画像の高さを取得する
     *
     * @return int サムネイル画像の高さ
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * サムネイル画像の高さをセットする
     *
     * @param int サムネイル画像の高さ
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * サムネイル画像の幅を取得する
     *
     * @return int サムネイル画像の幅
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * サムネイル画像の幅をセットする
     *
     * @param int サムネイル画像の幅
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }
}
