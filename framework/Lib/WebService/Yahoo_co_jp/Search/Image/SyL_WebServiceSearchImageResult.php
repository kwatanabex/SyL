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

/** Yahoo! Japan 画像検索レスポンス結果サムネイル情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResultThumbnail.php';

/**
 * Yahoo! Japan 画像検索レスポンス結果レコード情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2015 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchImageResult extends SyL_WebServiceResultAbstract
{
    /**
     * 画像ファイルのタイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * 画像ファイルに関連するテキストサマリー
     *
     * @var string
     */
     private $summary = null;
    /**
     * 画像ファイルのURL
     *
     * @var string
     */
     private $url = null;
    /**
     * 画像ファイルのリンクURL
     *
     * @var string
     */
     private $clickUrl = null;
    /**
     * 画像へのリンクを含むページのURL
     *
     * @var string
     */
     private $refererUrl = null;
    /**
     * ファイルサイズ
     *
     * @var string
     */
     private $fileSize = null;
    /**
     * ファイルフォーマット
     *
     * @var string
     */
     private $fileFormat = null;
    /**
     * 画像の高さ
     *
     * @var int
     */
     private $height = null;
    /**
     * 画像の幅
     *
     * @var int
     */
     private $width = null;
    /**
     * サムネイル画像
     *
     * @var SyL_WebServiceSearchResultThumbnail
     */
     private $thumbnail = null;
    /**
     * 画像ファイルの提供者
     *
     * @var string
     */
     private $publisher = null;
    /**
     * メディアオブジェクトの制限事項
     *
     * @var string
     */
     private $restrictions = null;
    /**
     * オーナーのコピーライト
     *
     * @var string
     */
     private $copyright = null;

    /**
     * 画像ファイルのタイトルを取得する
     *
     * @return string 画像ファイルのタイトル
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * 画像ファイルのタイトルをセットする
     *
     * @param string 画像ファイルのタイトル
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 画像ファイルに関連するテキストサマリーを取得する
     *
     * @return string 画像ファイルに関連するテキストサマリー
     */
    public function getSummary()
    {
        return $this->summary;
    }
    /**
     * 画像ファイルに関連するテキストサマリーをセットする
     *
     * @param string 画像ファイルに関連するテキストサマリー
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * 画像ファイルのURLを取得する
     *
     * @return string 画像ファイルのURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 画像ファイルのURLをセットする
     *
     * @param string 画像ファイルのURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 画像ファイルのリンクURLを取得する
     *
     * @return string 画像ファイルのリンクURL
     */
    public function getClickUrl()
    {
        return $this->clickUrl;
    }
    /**
     * 画像ファイルのリンクURLをセットする
     *
     * @param string 画像ファイルのリンクURL
     */
    public function setClickUrl($clickUrl)
    {
        $this->clickUrl = $clickUrl;
    }

    /**
     * 画像へのリンクを含むページのURLを取得する
     *
     * @return string 画像へのリンクを含むページのURL
     */
    public function getRefererUrl()
    {
        return $this->refererUrl;
    }
    /**
     * 画像へのリンクを含むページのURLをセットする
     *
     * @param string 画像へのリンクを含むページのURL
     */
    public function setRefererUrl($refererUrl)
    {
        $this->refererUrl = $refererUrl;
    }

    /**
     * ファイルサイズを取得する
     *
     * @return string ファイルサイズ
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }
    /**
     * ファイルサイズをセットする
     *
     * @param string ファイルサイズ
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;
    }

    /**
     * ファイルフォーマットを取得する
     *
     * @return string ファイルフォーマット
     */
    public function getFileFormat()
    {
        return $this->fileFormat;
    }
    /**
     * ファイルフォーマットをセットする
     *
     * @param string ファイルフォーマット
     */
    public function setFileFormat($fileFormat)
    {
        $this->fileFormat = $fileFormat;
    }

    /**
     * 画像の高さを取得する
     *
     * @return int 画像の高さ
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * 画像の高さをセットする
     *
     * @param int 画像の高さ
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * 画像の幅を取得する
     *
     * @return int 画像の幅
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * 画像の幅をセットする
     *
     * @param int 画像の幅
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * サムネイル画像を取得する
     *
     * @return SyL_WebServiceSearchResultThumbnail サムネイル画像
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }
    /**
     * サムネイル画像
     *
     * @param SyL_WebServiceSearchResultThumbnail サムネイル画像
     */
    public function setThumbnail(SyL_WebServiceSearchResultThumbnail $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * 画像ファイルの提供者を取得する
     *
     * @return string 画像ファイルの提供者
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
    /**
     * 画像ファイルの提供者をセットする
     *
     * @param string 画像ファイルの提供者
     */
    public function setPublisher($publisher)
    {
        $this->publisher = $publisher;
    }

    /**
     * メディアオブジェクトの制限事項を取得する
     *
     * @return string メディアオブジェクトの制限事項
     */
    public function getRestrictions()
    {
        return $this->restrictions;
    }
    /**
     * メディアオブジェクトの制限事項をセットする
     *
     * @param string メディアオブジェクトの制限事項
     */
    public function setRestrictions($restrictions)
    {
        $this->restrictions = $restrictions;
    }

    /**
     * オーナーのコピーライトを取得する
     *
     * @return int オーナーのコピーライト
     */
    public function getCopyright()
    {
        return $this->copyright;
    }
    /**
     * オーナーのコピーライトをセットする
     *
     * @param int オーナーのコピーライト
     */
    public function setCopyright($copyright)
    {
        $this->copyright = $copyright;
    }
}
