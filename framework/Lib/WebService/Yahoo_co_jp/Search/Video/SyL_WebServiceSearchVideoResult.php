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

/** Yahoo! Japan 画像検索レスポンス結果サムネイル情報クラス */
require_once dirname(__FILE__) . '/../SyL_WebServiceSearchResultThumbnail.php';

/**
 * Yahoo! Japan 動画検索レスポンス結果レコード情報クラス
 *
 * @package    SyL.Lib
 * @subpackage SyL.Lib.WebService
 * @author     Koki Watanabe <k.watanabe@syl.jp>
 * @copyright 2006-2012 k.watanabe
 * @license    http://www.opensource.org/licenses/lgpl-license.php
 * @version    CVS: $Id:$
 * @link       http://syl.jp/
 */
class SyL_WebServiceSearchVideoResult extends SyL_WebServiceResultAbstract
{
    /**
     * 動画ファイルのタイトル
     *
     * @var string
     */
     private $title = null;
    /**
     * 動画ファイルに関連するテキストサマリー
     *
     * @var string
     */
     private $summary = null;
    /**
     * 動画ファイルのURL
     *
     * @var string
     */
     private $url = null;
    /**
     * 動画ファイルのリンクURL
     *
     * @var string
     */
     private $clickUrl = null;
    /**
     * 動画コンテンツを含むページのURL
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
     * 動画から取得したキーフレームの高さ
     *
     * @var int
     */
     private $height = null;
    /**
     * 動画から取得したキーフレームの幅
     *
     * @var int
     */
     private $width = null;
    /**
     * 動画ファイルの時間
     *
     * @var int
     */
     private $duration = null;
    /**
     * オーディオチャネル数
     *
     * @var int
     */
     private $channels = null;
    /**
     * 動画ファイルがストリーミングかどうか
     *
     * @var bool
     */
     private $streaming = null;
    /**
     * サムネイル画像
     *
     * @var SyL_WebServiceSearchResultThumbnail
     */
     private $thumbnail = null;
    /**
     * 動画ファイルの提供者
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
     * 動画ファイルのタイトルを取得する
     *
     * @return string 動画ファイルのタイトル
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * 動画ファイルのタイトルをセットする
     *
     * @param string 動画ファイルのタイトル
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * 動画ファイルに関連するテキストサマリーを取得する
     *
     * @return string 動画ファイルに関連するテキストサマリー
     */
    public function getSummary()
    {
        return $this->summary;
    }
    /**
     * 動画ファイルに関連するテキストサマリーをセットする
     *
     * @param string 動画ファイルに関連するテキストサマリー
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;
    }

    /**
     * 動画ファイルのURLを取得する
     *
     * @return string 動画ファイルのURL
     */
    public function getUrl()
    {
        return $this->url;
    }
    /**
     * 動画ファイルのURLをセットする
     *
     * @param string 動画ファイルのURL
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * 動画ファイルのリンクURLを取得する
     *
     * @return string 動画ファイルのリンクURL
     */
    public function getClickUrl()
    {
        return $this->clickUrl;
    }
    /**
     * 動画ファイルのリンクURLをセットする
     *
     * @param string 動画ファイルのリンクURL
     */
    public function setClickUrl($clickUrl)
    {
        $this->clickUrl = $clickUrl;
    }

    /**
     * 動画コンテンツを含むページのURLを取得する
     *
     * @return string 動画コンテンツを含むページのURL
     */
    public function getRefererUrl()
    {
        return $this->refererUrl;
    }
    /**
     * 動画コンテンツを含むページのURLをセットする
     *
     * @param string 動画コンテンツを含むページのURL
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
     * 動画から取得したキーフレームの高さを取得する
     *
     * @return int 動画から取得したキーフレームの高さ
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * 動画から取得したキーフレームの高さをセットする
     *
     * @param int 動画から取得したキーフレームの高さ
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * 動画から取得したキーフレームの幅を取得する
     *
     * @return int 動画から取得したキーフレームの幅
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * 動画から取得したキーフレームの幅をセットする
     *
     * @param int 動画から取得したキーフレームの幅
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * 動画ファイルの時間を取得する
     *
     * @return int 動画ファイルの時間
     */
    public function getDuration()
    {
        return $this->duration;
    }
    /**
     * 動画ファイルの時間をセットする
     *
     * @param int 動画ファイルの時間
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * オーディオチャネル数を取得する
     *
     * @return int オーディオチャネル数
     */
    public function getChannels()
    {
        return $this->channels;
    }
    /**
     * オーディオチャネル数をセットする
     *
     * @param int オーディオチャネル数
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
    }

    /**
     * 動画ファイルがストリーミングかどうかを取得する
     *
     * @return bool 動画ファイルがストリーミングかどうか
     */
    public function getStreaming()
    {
        return $this->streaming;
    }
    /**
     * 動画ファイルがストリーミングかどうかをセットする
     *
     * @param bool 動画ファイルがストリーミングかどうか
     */
    public function setStreaming($streaming)
    {
        if (is_bool($streaming)) {
            $this->streaming = $streaming;
        } else {
            $this->streaming = ($streaming == 'true');
        }
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
     * 動画ファイルの提供者を取得する
     *
     * @return string 動画ファイルの提供者
     */
    public function getPublisher()
    {
        return $this->publisher;
    }
    /**
     * 動画ファイルの提供者をセットする
     *
     * @param string 動画ファイルの提供者
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
